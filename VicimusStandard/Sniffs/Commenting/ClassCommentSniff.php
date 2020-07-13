<?php

namespace Vicimus\Standard\Sniffs\Commenting;

use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FileCommentSniff;

class ClassCommentSniff extends FileCommentSniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (stripos($phpcsFile->getFilename(), '/tests/') !== false) {
            return;
        }

        $this->currentFile = $phpcsFile;
        $tokens    = $phpcsFile->getTokens();
        $type      = strtolower($tokens[$stackPtr]['content']);
        $errorData = array($type);
        $find   = Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;
        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'no');
            return;
        }
        $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'yes');
        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr, 'WrongStyle');
            return;
        }
        // Check each tag.
        //$this->processTags($phpcsFile, $stackPtr, $tokens[$commentEnd]['comment_opener']);
    }

    /**
     * Process the version tag.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param array                $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processVersion($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }
            $content = $tokens[($tag + 2)]['content'];
            if ((strstr($content, 'Release:') === false)) {
                $error = 'Invalid version "%s" in doc comment; consider "Release: <package_version>" instead';
                $data  = array($content);
                $phpcsFile->addWarning($error, $tag, 'InvalidVersion', $data);
            }
        }
    }
}
