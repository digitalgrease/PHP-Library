<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 */

namespace GreasyLab\Library\Utils;

/**
 * File utility functions.
 * 
 * @author Tom Gray
 */
class FileUtils
{
    /**
     * Get a list of all the files and directories modified between the given
     * timestamps, inclusive.
     * DO TG FURTHER DEVELOPMENT: Add a recursive flag.
     * 
     * @param string $directory The top level directory to search in.
     * @param int    $startTime The start of the range to search for files
     *                          modified between.
     * @param int    $endTime   The end of the range to search for files
     *                          modified between.
     * 
     * @return array Collection of arrays with the following keys:
     *               'isFile' => (bool) True if this is a file, false if it is
     *                                  a directory.
     *               'path' => (string) Name of the file.
     *               'timestamp' => (int) The file's last modified time.
     */
    public static function getModifiedFiles($directory, $startTime, $endTime)
    {
        $files = [];

        $dirListing = glob($directory . '*', GLOB_MARK);

        foreach ($dirListing as $path) {
            $file['isFile'] = is_file($path);
            $file['path'] = $path;
            $file['timestamp'] = filemtime($path);
            
            if (
                $startTime <= $file['timestamp']
                && $endTime >= $file['timestamp']
            ) {
                $files[] = $file;
            }
            
            if (is_dir($path)) {
                $files = array_merge(
                    $files,
                    FileUtils::getModifiedFiles(
                        $path,
                        $startTime,
                        $endTime
                    )
                );
            }
        }
        return $files;
    }
    
    /**
     * Get a list of all the files and directories in the given directory.
     * 
     * @param string $directory The top level directory to search in.
     * @param bool   $recurse   True to recurse through all the sub-directories.
     *                          False to just return files and directories in
     *                          the given directory.
     * 
     * @return array Collection of strings which are the paths to the files and
     *               directories.
     */
    public static function getFiles($directory, $recurse = false)
    {
        $files = [];

        $dirListing = glob($directory . '*', GLOB_MARK);

        foreach ($dirListing as $path) {
            $files[] = $path;
            
            if ($recurse && is_dir($path)) {
                $files = array_merge(
                    $files,
                    FileUtils::getFiles(
                        $path,
                        true
                    )
                );
            }
        }
        return $files;
    }
}
