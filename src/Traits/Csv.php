<?php

namespace ClockIt\Baserepo\Traits;

trait Csv {

    /**
     * CSVファイルを生成する
     *
     * @param $filename
     * @return string
     */
    public static function createCsv($filename) {
        $csv_file_path = storage_path('app/'.$filename);
        $result = fopen($csv_file_path, 'w');
        if ($result === FALSE) {
            throw new Exception('ファイルの書き込みに失敗しました。');
        } else {
            fwrite($result, pack('C*',0xEF,0xBB,0xBF)); // BOM をつける
        }
        fclose($result);

        return $csv_file_path;
    }

    /**
     * CSVファイルに書き出す
     *
     * @param $filepath
     * @param $records
     */
    public static function write($filepath, $records) {
        $result = fopen($filepath, 'a');

        // ファイルに書き出し
        fputcsv($result, $records);

        fclose($result);
    }

    /**
     * CSVファイルの削除
     *
     * @param $filename
     * @return bool
     */
    public static function purge($filename) {
        return unlink(storage_path('app/'.$filename));
    }
}
