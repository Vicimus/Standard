<?php

namespace Vicimus\Standard\Sniffs\Classes;

use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FileCommentSniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

class VisibilityOrderSniff extends FileCommentSniff
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
        $previousVisString = null;

        while (($propertyTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, $findPropertiesStartTokenPointer, $classToken['scope_closer'])) !== null) {
            $propertyToken = $tokens[$propertyTokenPointer];
            $visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $propertyTokenPointer - 1);
            $visibilityModifiedToken = $tokens[$visibilityModifiedTokenPointer];

            $visibility = $this->visibilityValue($visibilityModifiedToken['code']);
            $visString = $this->visibility($visibilityModifiedToken['code']);
            $visCode = $visibilityModifiedToken['code'];

            if ($visibility === null) {
                $findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
                continue;
            }


            $name = substr($propertyToken['content'], 1);
            if ($previousVisibility && $previousVisibility > $visibility) {
                $error = 'Property ['. $visString . ' ' . $name.'] should be before property ['. $previousVisString . ' ' .$previousName.']';

                $phpcsFile->addError(
                    $error,
                    $propertyTokenPointer,
                    'Invalid'
                );
            }

            $previousName = $name;
            $previousVisibility = $visibility;
            $previousVisCode = $visCode;
            $previousVisString = $visString;
            $findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
        }
    }

    protected function visibilityValue($code)
    {
        if ($code === T_PRIVATE) {
            return 2;
        }

        if ($code === T_PROTECTED) {
            return 1;
        }

        if ($code === T_PUBLIC) {
            return 0;
        }

        return null;
    }

    protected function visibility($code)
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
