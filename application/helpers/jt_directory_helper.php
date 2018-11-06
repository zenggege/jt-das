<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    if ( ! function_exists('dir_create'))
    {
        /**
        * 创建目录
        * 
        * @param    string  $path   路径
        * @param    string  $mode   属性
        * @return   string  如果已经存在则返回true，否则为flase
        */
        function dir_create($path, $mode = 0777) {
            if(is_dir($path)) return TRUE;
            $ftp_enable = 0;
            $path = dir_path($path);
            $temp = explode('/', $path);
            $cur_dir = '';
            $max = count($temp) - 1;
            for($i=0; $i<$max; $i++) {
                $cur_dir .= $temp[$i].'/';
                if (@is_dir($cur_dir)) continue;
                @mkdir($cur_dir, 0777,true);
                @chmod($cur_dir, 0777);
            }
            return is_dir($path);
        }
    }
    
    if ( ! function_exists('dir_path'))
    {
        /**
        * 转化 \ 为 /
        * 
        * @param    string  $path   路径
        * @return   string  路径
        */
        function dir_path($path) {
            $path = str_replace('\\', '/', $path);
            if(substr($path, -1) != '/') $path = $path.'/';
            return $path;
        }
    }

    if ( ! function_exists('move_folder'))
    {
        /**
         * 递归移动源目录（包括文件和子文件）到目的目录【或移动源文件到新文件】
         * @param [string] $source 源目录或源文件
         * @param [string] $target 目的目录或目的文件
         * @return boolean true
         */

        function move_folder($source, $target){

            if(!file_exists($source))return false; //如果源目录/文件不存在返回false

            //如果要移动文件
            if(filetype($source) == 'file'){
                $basedir = dirname($target);
                if(!is_dir($basedir))mkdir($basedir); //目标目录不存在时给它创建目录
                copy($source, $target);
                unlink($source);

            }else{ //如果要移动目录

                if(!file_exists($target))mkdir($target); //目标目录不存在时就创建

                $files = array(); //存放文件
                $dirs = array(); //存放目录
                $fh = opendir($source);

                if($fh != false){
                    while($row = readdir($fh)){
                        $src_file = $source . '/' . $row; //每个源文件
                        if($row != '.' && $row != '..'){
                            if(!is_dir($src_file)){
                                $files[] = $row;
                            }else{
                                $dirs[] = $row;
                            }
                        }
                    }
                    closedir($fh);
                }

                foreach($files as $v){
                     copy($source . '/' . $v, $target . '/' . $v);
                     unlink($source . '/' . $v);
                }

               if(count($dirs)){
                    foreach($dirs as $v){
                        move_folder($source . '/' . $v, $target . '/' . $v);
                    }
                }
            }
            return true;
        }
    }