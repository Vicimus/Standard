<?php

namespace Vicimus\Standard\Sniffs\Classes;

use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FileCommentSniff;

class AlphabeticalMethodSniff extends FileCommentSniff
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
        if ($this->isTest($phpcsFile)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $function = $stackPtr;

        $scopes = array(
            0 => T_PUBLIC,
            1 => T_PROTECTED,
            2 => T_PRIVATE,
        );

        $whitelisted = array(
            '__construct',
            'setUp',
            'tearDown',
        );

        $previousName = '';

        while ($function) {
            $end = null;

            if (isset($tokens[$stackPtr]['scope_closer'])) {
                $end = $tokens[$stackPtr]['scope_closer'];
            }

            $function = $phpcsFile->findNext(
                T_FUNCTION,
                $function + 1,
                $end
            );

            if (isset($tokens[$function]['parenthesis_opener'])) {
                $scope = $phpcsFile->findPrevious($scopes, $function -1, $stackPtr);
                $name = $phpcsFile->findNext(
                    T_STRING,
                    $function + 1,
                    $tokens[$function]['parenthesis_opener']
                );

                $currentName = $tokens[$name]['content'];


                if ($scope
                    && $name
                    && !in_array(
                        $tokens[$name]['content'],
                        $whitelisted
                    )
                ) {
                    $current = array_keys($scopes,  $tokens[$scope]['code']);
                    $current = $current[0];


                    if (isset($previous) && $previousName && strcmp($previousName, $currentName) > 0 && $current === $previous) {
                        $error = 'Method ['. $currentName.'] should be alphabetically before method ['.$previousName.']';

                        $phpcsFile->addError(
                            $error,
                            $scope,
                            'Invalid'
                        );
                    }

                    $previous = $current;
                    $previousName = $currentName;
                }
            }
        }
    }

    /**
     * Check if the file is a test or not
     *
     * @param File $file The file object
     *
     * @return bool
     */
    protected function isTest(File $file)
    {
        return stripos($file->path, '/tests/') !== false;
    }
}
