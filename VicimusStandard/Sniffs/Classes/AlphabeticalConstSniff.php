<?php

namespace Vicimus\Standard\Sniffs\Classes;

use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FileCommentSniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

class AlphabeticalConstSniff extends FileCommentSniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return string[]
     */
    public function register()
    {
        return [
            T_CLASS,
            T_INTERFACE,
        ];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token
     *                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $classToken = $tokens[$stackPtr];
        $properties = $this->getProperties($phpcsFile, $classToken, $tokens);
    }

    protected function getProperties(File $phpcsFile, $classToken, $tokens)
    {
        $findPropertiesStartTokenPointer = $classToken['scope_opener'] + 1;
        $previousName = '';
        $previousVisibility = null;
        $previousVisCode = null;

        while (($propertyTokenPointer = TokenHelper::findNext($phpcsFile, T_CONST, $findPropertiesStartTokenPointer, $classToken['scope_closer'])) !== null) {
            $propertyToken = $tokens[$propertyTokenPointer];
            $visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $propertyTokenPointer - 1);
            $visibilityModifiedToken = $tokens[$visibilityModifiedTokenPointer];

            $visibility = $this->visibility($visibilityModifiedToken['code']);
            $visCode = $visibilityModifiedToken['code'];
            if (!$visibility) {
                $findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
                continue;
            }



            //$name = substr($propertyToken['content'], 1);
            $name = $tokens[TokenHelper::findNextEffective($phpcsFile, $findPropertiesStartTokenPointer + 1)]['content'];
            if ($name === 'use') {
                $findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
                continue;
            }

            if ($name === $visibility) {
                $findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
                continue;
            }

            if ($previousName && strcmp($name, $previousName) < 0 && $visCode === $previousVisCode) {
                $error = '['. $visibility . ' const ' . $name.'] should be alphabetically before ['. $previousVisibility . ' const ' .$previousName.']';

                $phpcsFile->addError(
                    $error,
                    $propertyTokenPointer,
                    'Invalid'
                );
            }

            $previousName = $name;
            $previousVisibility = $visibility;
            $previousVisCode = $visCode;
            $findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
        }
    }

    protected function visibility($code): ?string
    {
        if ($code === T_PRIVATE) {
            return 'private';
        }

        if ($code === T_PROTECTED) {
            return 'protected';
        }

        if ($code === T_PUBLIC) {
            return 'public';
        }

        return null;
    }
}
