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

    /**
     * CSVデータIterator
     *
     * @param array $captionMappings
     * @param iterable $rows
     * @return \Generator
     */
    public function createDataIterator(array $captionMappings, iterable $rows): \Generator
    {
        $captions = null;
        foreach ($rows as $i => $row) {
            if ($i === 0) {
                $captions = array_flip(array_filter(array_map(function ($caption) use ($row) {
                    return array_search($caption, $row, true);
                }, $captionMappings), 'is_int'));
                ksort($captions);
            } else {
                yield array_combine($captions, array_intersect_key($row, $captions));
            }
        }
    }

    /**
     * CSV SqlFileObject作成
     *
     * @param $fileName
     * @param $captionMappings
     * @return \SplFileObject
     */
    public function createSqlFileObject($fileName, $captionMappings): \SplFileObject
    {
        $file = new \SplFileObject($fileName);
        $file->setFlags(
            \SplFileObject::READ_CSV | // foreachの反復処理をfgetcsvで行う
            \SplFileObject::SKIP_EMPTY | // 空行を読み飛ばす (これが無いと空行を [null] として読み取ってしまう)
            \SplFileObject::READ_AHEAD | // 空行を読み飛ばすためには必須
            \SplFileObject::DROP_NEW_LINE // 空行を読み飛ばすためには必須
        );

        return $file;
    }
}
