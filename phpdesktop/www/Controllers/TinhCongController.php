<?php

namespace TINHCONG\Controllers;

use TINHCONG\Core\Controller;
use TINHCONG\Config\ExeclDB;
use TINHCONG\Controllers\ExcelsController;

class TinhCongController extends Controller
{
    private $datas = [];
    private $dataWorkTimes = [];
    private $file_name;
    private $full_name;


    private $excelsController;
    public function __construct()
    {
        $this->excelsController = new ExcelsController();
        // echo "<pre>";

        // $this->workTime();
        // print_r(self::$datas);
        // die;
    }
    public function readExcel($path, $fileType, $file_name)
    {
        $results = $this->excelsController->readExcel($path, $fileType, $file_name);
        $_SESSION['results'] = $results;
        $this->refreshData($results);
    }

    public function upload()
    {
        $results = $this->excelsController->upload($_FILES);
        $_SESSION['results'] = $results;
        $this->refreshData($results);
    }

    public function menuReadExcel()
    {
        $results = $this->excelsController->menuReadExcel($_POST);
        $_SESSION['results'] = $results;
        $this->refreshData($results);
    }

    private function refreshData($results)
    {
        // print_r($results);
        $this->datas = $results['data'];
        $this->file_name = $results['file_name'];
        $this->full_name = $results['file_fullname'];

        $this->showExcel($this->datas, $this->file_name, $this->full_name);
    }


    public function showWorkTime()
    {
   
        $dataWorkTimes = $_SESSION['results']['data'];
        $file_name = $_SESSION['results']['file_name'];
        $full_name  = $_SESSION['results']['file_fullname'];

        foreach (array_slice($dataWorkTimes, 1) as $rowKey => $row) {
            foreach (array_slice($row, 3) as $itemKey => $item) {
                $dataWorkTimes[$rowKey + 1][$itemKey + 3] = $this->caculateWorkTime($item);
                
            }
        }
        $this->showExcel($dataWorkTimes, $file_name, $full_name);

    }

    private function showExcel($datas, $file_name = "", $full_name = "")
    {
        $this->datas = $datas;
        $d['datas'] = $datas;
        $d['file_name'] = $file_name;
        $d['file_fullname'] = $full_name;
        $this->set($d);
        $this->render("dataTable", "emtylayout");
    }

    private function caculateWorkTime($time)
    {

        if ($time == null) {
            return null;
        }

        $time = str_replace(" ", '', $time);
        $splipTime = explode('-', $time);

        // N???u ch??? qu???t 1 l???n th?? kh??ng ???????c t??nh
        if (count($splipTime) <= 1) {
            return null;
        }

        //N???u qu??t >= 2 l???n th?? l???y s??? ?????u v?? cu???i;
        $startTime = $this->toMinute($this->getStartTime($splipTime));
        $endTime = $this->toMinute($this->getStopTime($splipTime));

        // t??nh th???i gian l??m
        // Th???i gian ?????n v?? v??? tr??? ??i ngh??? tr??a 90p
        if (ctype_digit($startTime) && ctype_digit($endTime) && $startTime != $endTime) {
            $workTime =  $endTime - $startTime - 90;
            return $this->toHour($workTime);
        } else {
            return "Qu???t thi???u";
        }
    }

    private function getStartTime($splipTime)
    {
        // N???u qu???t th??? tr?????c 7h s??? kh??ng ???????c t??nh
        foreach ($splipTime as $time) {
            if ($this->toMinute($time) >= $this->toMinute("07:00")) {
                return $time;
            }
        }
        return null;
    }
    private function getStopTime($splipTime)
    {
        // N???u qu???t th??? sau 19h s??? kh??ng ???????c t??nh
        foreach (array_reverse($splipTime) as $time) {
            if ($this->toMinute($time) <= $this->toMinute("19:00")) {
                return $time;
            }
        }
        return null;
    }


    private function toMinute($time)
    {
        $time = str_replace(" ", '', $time);

        $splipTime = explode(':', $time);
        $minute = "";

        if (ctype_digit($splipTime[0]) && ctype_digit(end($splipTime))) {
            $minute = ($splipTime[0] * 60 + (int)end($splipTime));
        }
        return $minute;
    }

    private function toHour($time)
    {
        if ($time < 1 || !is_numeric($time)) {
            return "";
        }
        $hour = floor($time / 60);
        $minute = $time % 60;

        return sprintf("%02d", $hour) . " : " . sprintf("%02d", $minute);
    }
}?>

<?php
echo "Hello World";
?>