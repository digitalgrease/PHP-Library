<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 */

namespace DigitalGrease\Library\Files;

require_once 'FileSystemInterface.php';
require_once 'Net/SFTP.php';

use DigitalGrease\Library\Files\FileSystemInterface;

/**
 * Provides access to a remote file system.
 * 
 * @author Tom Gray
 */
class RemoteFileSystem implements FileSystemInterface
{
    
    /**
     * Denotes to read data from a local file.
     * 
     * @var int
     */
    const NET_SFTP_LOCAL_FILE = 1;
    
    /**
     * The connection to the remote host.
     * 
     * @var \Net_SFTP
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
     * Get the elements of a remote host file path.
     * 
     * @param string $remotePath
     * 
     * @return array
     */
    public static function getRemotePathElements($remotePath)
    {
        $parts['user'] = strstr($remotePath, '@', true);
        $parts['host'] = substr(
            $remotePath,
            strpos($remotePath, '@') + 1,
            strpos($remotePath, ':') - strpos($remotePath, '@') - 1
        );
        $parts['path'] = substr($remotePath, strpos($remotePath, ':') + 1);
        return $parts;
    }

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
     * {@inheritdoc}
     */
    public function filemtime($path)
    {
        return $this->connection->filemtime($path);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContent($path)
    {
        return $this->connection->get($path);
    }
    
    /**
     * @inheritDoc
     */
    public function getDetailedFileList($path)
    {
        $fileList = [];
        
        $files = $this->connection->rawlist($path);
        if ($files) {
            unset($files['.']);
            unset($files['..']);
            $fileList = $files;
        }
        
        return $fileList;
    }
    
    /**
     * @inheritDoc
     */
    public function getFileList($path)
    {
        $fileList = [];
        
        $files = $this->connection->nlist($path);
        if ($files) {
            unset($files[array_search('.', $files)]);
            unset($files[array_search('..', $files)]);
            $fileList = $files;
        }
        
        return $fileList;
    }
    
    /**
     * @inheritDoc
     */
    public function getModifiedFiles($directory, $startTime, $endTime)
    {
        throw new \Exception('To be implemented');
    }
    
    /**
     * Get the size of a file in bytes.
     * Files larger than 4GB will show up as being exactly 4GB.
     * 
     * @param string $path
     * 
     * @return int|bool The size of the file in bytes. False if the file does
     *  not exist.
     */
    public function getSize($path)
    {
        return $this->connection->size($path);
    }
    
    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        if ($data = $this->connection->stat($path)) {
            return FileSystemInterface::DIRECTORY === $data['type'];
        }
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        if ($data = $this->connection->stat($path)) {
            return FileSystemInterface::REGULAR_FILE === $data['type'];
        }
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function mkdir($path, $mode = -1, $recursive = false)
    {
        return $this->connection->mkdir($path, $mode, $recursive);
    }
    
    /**
     * {@inheritdoc}
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
     * @inheritDoc
     */
    public function readFromEof($filePath, $marker = PHP_EOL)
    {
        throw new \Exception('To be implemented!');
    }
    
    /**
     * {@inheritdoc}
     */
    public function rmdir($dir)
    {
        return $this->connection->delete($dir);
    }
    
    /**
     * {@inheritdoc}
     */
    public function touch($path, $time = null, $atime = null)
    {
        return $this->connection->touch($path, $time, $atime);
    }
    
    /**
     * {@inheritdoc}
     */
    public function unlink($path)
    {
        return $this->connection->delete($path, false);
    }
}
