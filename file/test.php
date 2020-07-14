<?php

/**
 * 递归打印文件路径下的所有文件夹及文件（树形结构）
 *
 * @param string    $dir        文件路径
 * @param integer   $level      文件夹层级，默认即可
 * @return void
 */
function printDir(string $dir, int $level = 1) 
{
    foreach (scandir($dir) as $file) {
        if (!in_array($file, ['.', '..'])) {
            if (is_dir($dir . DIRECTORY_SEPARATOR .$file)) {
                echo str_repeat(' ', $level * 2) . '|--' . $file . "[dir]\n";
                printDir($dir . DIRECTORY_SEPARATOR .$file, $level + 1);
            } else {
                echo str_repeat(' ', $level * 2) . '|--' . $file . "[file]\n";
            }
        }
    }
}

// printDir('./');


/**
 * 递归遍历文件路径下的所有文件夹及文件
 *
 * @param string $dir   文件路径
 * @return array        返回值中，值为数组时为文件夹（其中键为文件夹名称，值为文件夹包含的文件）；值为字符串时为文件（其中值为文件名称）
 */
function getDir(string $dir) : array
{
    $ret = [];
    foreach (scandir($dir) as $file) {
        if (!in_array($file, ['.', '..'])) {
            if (is_dir($dir . DIRECTORY_SEPARATOR .$file)) {
                $files = getDir($dir . DIRECTORY_SEPARATOR .$file);
                $ret[] = array($file => $files);
            } else {
                $ret[] = $file;
            }
        }
    }
    return $ret;
}

// print_r(getDir('./'));