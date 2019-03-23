<?php

class uploadController extends API
{
    function uploadImgAction()
    {
        $fileName = $_FILES["file"]["name"];
        $fileType = $_FILES["file"]["type"];
        $fileSize = $_FILES["file"]["size"];
        $fileTmp = $_FILES["file"]["tmp_name"];
        if ($fileType == "image/pjpeg"
            || $fileType == "image/jpeg"
            || $fileType == "image/gif"
            || $fileType == "image/jpg"
            || $fileType == "image/x-png"
            || $fileType == "image/png"
        ) {
            if ($fileSize < 5242880) {
                if ($_FILES["file"]["error"] > 0) {
                    echo '{"code": "1", "mes": "Return Code: ' . $_FILES["file"]["error"] . '"}';
                } else {
                    if (file_exists(__STATIC__ . "upload" . DS . $fileName)) {
                        echo '{"code": "1", "mes": "' . $fileName . ' already exists."}';
                    } else {
                        $judgeType = $this->fileTypeJudge($fileTmp);
                        if ($judgeType == 'jpg' || $judgeType == 'png' || $judgeType == 'gif') {
                            $fileName = $_SESSION['user']['user'] . "_" . time() . '.' . $judgeType;
                            $ret = move_uploaded_file($fileTmp, __STATIC__ . "upload" . DS . $fileName);
                            if ($ret) {
                                $fileUrl = 'http://' . $_SERVER['SERVER_NAME'] . __WEB__ . 'Static/upload/' . $fileName;
                                echo '{"code": "0", "url": "' . $fileUrl . '"}';
                            } else {
                                echo '{"code": "1", "mes": "service error!"}';
                            }
                        } else {
                            echo '{"code": "1", "mes": "只能上传 jpg,gif,png 格式的图片。"}';
                        }
                    }
                }
            } else {
                echo '{"code": "1", "mes": "图片大小不得超过5MB！"}';
            }
        } else {
            echo '{"code": "1", "mes": "只能上传 jpg,gif,png 格式的图片。"}';
        }
    }
}