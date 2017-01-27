<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Tuesday 17th May 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Files;

/**
 * Defines an API for file systems to implement.
 * 
 * @author Tom Gray
 */
interface FileSystemInterface
{
    
    /**
     * Denotes the type of a file as a regular file.
     * 
     * @var int
     */
    const REGULAR_FILE = 1;
    
    /**
     * Denotes the type of a file as a directory.
     * 
     * @var int
     */
    const DIRECTORY = 2;
    
    /**
     * Denotes the type of a file as a symbolic link.
     * 
     * @var int
     */
    const SYMBOLIC_LINK = 3;
    
    /**
     * Denotes the type of file as special???
     * 
     * @var int
     */
    const SPECIAL = 4;
    
    /**
     * Get the modified timestamp of a file.
     * 
     * @param string $path
     * 
     * @return int|bool Timestamp or false if the path does not exist.
     */
    public function filemtime($path);
    
    /**
     * Get the content of a file.
     * 
     * @param string $path
     * 
     * @return string
     */
    public function getContent($path);
    
    /**
     * Get a detailed list of information of the files in a directory.
     * 
     * @param string $path
     * 
     * @return array Associative array of associative arrays where the keys of
     *  the outer array are an unordered list of file and directory names not
     *  including '.' and '..'. Each inner array contains the following keys:
     *      'size' => (int) The size of the file in bytes.
     *      'uid' => (int) The user ID of the file owner.
     *      'gid' => (int) ??? Example value = 1000.
     *      'permissions' => (int) The file permissions.
     *      'mode' => (int) ??? Example value = 16832.
     *      'type' => (int) The type of file as defined by the constants in this
     *                      file.
     *      'atime' => (int) The time the file was last accessed.
     *      'mtime' => (int) The time the file was last modified.
     */
    public function getDetailedFileList($path);
    
    /**
     * Get a list of files in a directory.
     * 
     * @param string $path
     * 
     * @return array Array of strings which is an unordered list of file and
     *  directory names not including '.' and '..'.
     */
    public function getFileList($path);
    
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
     * @return array Array of associative arrays with the following keys:
     *               'isFile' => (bool) True if this is a file, false if it is
     *                                  a directory.
     *               'path' => (string) Name of the file.
     *               'timestamp' => (int) The file's last modified time.
     */
    public function getModifiedFiles($directory, $startTime, $endTime);
    
    /**
     * Get the size of a file in bytes.
     * 
     * @param string $path
     * 
     * @return int|bool The size of the file in bytes. False if the file does
     *  not exist.
     */
    public function getSize($path);
    
    /**
     * Get whether a path exists and is a directory.
     * 
     * @param string $path
     * 
     * @return bool True if the path exists and is a directory, false otherwise.
     */
    public function isDir($path);
    
    /**
     * Get whether a path exists and is a file.
     * 
     * @param string $path
     * 
     * @return bool True if the path exists and is a file, false otherwise.
     */
    public function isFile($path);
    
    /**
     * Create a directory.
     * 
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     * 
     * @return bool True if the directory is created or exists.
     *              False if the directory could not be created.
     */
    public function mkdir($path, $mode = 0755, $recursive = false);
    
    /**
     * Create a file with the given content.
     * 
     * @param string $dest
     * @param string $data
     * 
     * @return bool True if file created, false otherwise.
     */
    public function putContent($dest, $data);
    
    /**
     * Delete a directory and all its contents.
     * 
     * @param string $dir
     * 
     * @return bool
     */
    public function rmdir($dir);
    
    /**
     * Set the modification and access time of a file.
     * Creates the file if it does not exist.
     * 
     * @param string $path
     * @param int    $time
     * @param int    $atime
     * 
     * @return bool True on success, false on failure.
     */
    public function touch($path, $time = null, $atime = null);
    
    /**
     * Delete a file or link.
     * 
     * @param string $path
     * 
     * @return bool
     */
    public function unlink($path);
}
