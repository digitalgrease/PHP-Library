<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 */

namespace GreasyLab\Library\Utils;

require_once 'Net/SFTP.php';

/**
 * Connection to a remote host.
 * 
 * @author Tom Gray
 */
class RemoteHost
{
    
    /**
     * Denotes the type of a file as a regular file.
     * 
     * @var int
     */
    const NET_SFTP_TYPE_REGULAR_FILE = 1;
    
    /**
     * Denotes the type of a file as a directory.
     * 
     * @var int
     */
    const NET_SFTP_TYPE_DIRECTORY = 2;
    
    /**
     * Denotes the type of a file as a symbolic link.
     * 
     * @var int
     */
    const NET_SFTP_TYPE_SYMLINK = 3;
    
    /**
     * Denotes the type of file as special???
     * 
     * @var int
     */
    const NET_SFTP_TYPE_SPECIAL = 4;
    
    /**
     * Denotes to read data from a local file.
     * 
     * @var int
     */
    const NET_SFTP_LOCAL_FILE = 1;
    
    /**
     * The connection to the remote host.
     * 
     * @var Net_SFTP
     */
    protected $connection;
    
    /**
     * The name or IP address of the remote host.
     * 
     * @var string
     */
    protected $hostName;
    
    /**
     * The username to login to the remote host.
     * 
     * @var string
     */
    protected $username;
    
    /**
     * The password to login to the remote host.
     * 
     * @var string
     */
    protected $password;
    
    /**
     * Determine if a string is a remote host path.
     * 
     * @param string $string
     * 
     * @return bool
     */
    public static function isRemoteHostPath($string)
    {
        return (bool)strstr($string, '@');
    }

    /**
     * Construct the connection.
     * 
     * @param string $hostName The name or IP address of the remote host.
     * @param string $username The username to login to the remote host.
     * @param string $password The password to login to the remote host.
     */
    public function __construct($hostName, $username, $password)
    {
        $this->hostName = $hostName;
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Establish a connection and log in to the remote host.
     * 
     * @return boolean True if connected and authenticated.
     *                 False if connection could not be established.
     */
    public function login()
    {
        $this->connection = new \Net_SFTP($this->hostName);
        return $this->connection->login($this->username, $this->password);
    }
    
    /**
     * Delete a directory and all its contents.
     * 
     * @param string $dir
     * 
     * @return bool
     */
    public function rmdir($dir)
    {
        return $this->connection->delete($dir);
    }
    
    /**
     * Get the modified timestamp of a file.
     * 
     * @param string $path
     * 
     * @return int|bool Timestamp or false if the path does not exist.
     */
    public function filemtime($path)
    {
        return $this->connection->filemtime($path);
    }
    
    /**
     * Get the content of a file from the remote host.
     * 
     * @param string $path
     * 
     * @return string
     */
    public function getContent($path)
    {
        return $this->connection->get($path);
    }
    
    /**
     * Get a detailed list of information of the files at the given path on the
     * remote host.
     * 
     * @param string $path
     * 
     * @return array
     */
    public function getDetailedFileList($path)
    {
        return $this->connection->rawlist($path);
    }
    
    /**
     * Get a list of files in a directory on the remote host.
     * 
     * @param string $path
     * 
     * @return array|bool Array of strings which are the names of the files or
     *                    false if the path is not a directory.
     */
    public function getFileList($path)
    {
        return $this->connection->nlist($path);
    }
    
    /**
     * Get whether the given path exists and is a directory.
     * 
     * @param string $path
     * 
     * @return bool True if the path exists and is a directory, false otherwise.
     */
    public function isDir($path)
    {
        if ($data = $this->connection->stat($path)) {
            return self::NET_SFTP_TYPE_DIRECTORY === $data['type'];
        }
        return false;
    }
    
    /**
     * Get whether the given path exists and is a file.
     * 
     * @param string $path
     * 
     * @return bool True if the path exists and is a file, false otherwise.
     */
    public function isFile($path)
    {
        if ($data = $this->connection->stat($path)) {
            return self::NET_SFTP_TYPE_REGULAR_FILE === $data['type'];
        }
        return false;
    }
    
    /**
     * Create a directory on the remote host.
     * 
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     * 
     * @return bool True if the directory is created.
     *              False if the directory could not be created.
     */
    public function mkdir($path, $mode = -1, $recursive = false)
    {
        return $this->connection->mkdir($path, $mode, $recursive);
    }
    
    /**
     * Put a file with the given content onto the remote host.
     * 
     * @param string $dest
     * @param string $data
     * 
     * @return bool True if file created, false otherwise.
     */
    public function putContent($dest, $data)
    {
        return $this->connection->put($dest, $data);
    }
    
    /**
     * Copy a local file onto the remote host.
     * 
     * @param string $src
     * @param string $dest
     * 
     * @return bool True if file copied, false otherwise.
     */
    public function putFile($src, $dest)
    {
        return $this->connection->put($dest, $src, self::NET_SFTP_LOCAL_FILE);
    }
    
    /**
     * Set the modification and access time of a file on the remote host.
     * Creates the file if it does not exist.
     * 
     * @param string $path
     * @param int    $time
     * @param int    $atime
     * 
     * @return bool True on success, false on failure.
     */
    public function touch($path, $time = null, $atime = null)
    {
        return $this->connection->touch($path, $time, $atime);
    }
    
    /**
     * Delete a file or link.
     * 
     * @param string $path
     * 
     * @return bool
     */
    public function unlink($path)
    {
        return $this->connection->delete($path, false);
    }
}
