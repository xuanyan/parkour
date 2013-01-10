<?php

/*
* 计算STL文件模型体积
*
* @copyright (c) 2012 Atom Projects More info http://Atom.com
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
* @author hanlei <lhan@myspace.cn>
* @copyright http://schmidtcds.com/2011/07/php-stl-files/
*/

class Stl
{
    public $filepath;

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * detec function
     *
     * @return array(x_dim,y_dim,z_dim)
     */
    public function detect()
    {
        if (!is_file($this->filepath)){
            throw new Exception("file stl path error");
        }

        $x_max = 0;
        $y_max = 0;
        $z_max = 0;
        $x_min = 0;
        $y_min = 0;
        $z_min = 0;
        $filepath = $this->filepath;

        $fp = fopen($filepath, "rb");
        //$section = file_get_contents($filepath, NULL, NULL, 0, 79);
        fseek($fp, 80);
        $data = fread($fp, 4);
        $numOfFacets = unpack("I", $data);
        for ($i = 0; $i < $numOfFacets[1]; $i++){
            //Start Normal Vector
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $normalVectorsX[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $normalVectorsY[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $normalVectorsZ[$i] = $hold[1];
            //End Normal Vector

            //Start Vertex1
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex1X[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex1Y[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex1Z[$i] = $hold[1];
            //End Vertex1

            //Start Vertex2
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex2X[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex2Y[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex2Z[$i] = $hold[1];
            //End Vertex2

            //Start Vertex3
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex3X[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex3Y[$i] = $hold[1];
            $data = fread($fp, 4);
            $hold = unpack("f", $data);
            $vertex3Z[$i] = $hold[1];
            //End Vertex3

            //Attribute Byte Count
            $data = fread($fp, 2);
            $hold = unpack("S", $data);
            $abc[$i] = $hold[1];

            $x_vals = array($vertex1X[$i], $vertex2X[$i], $vertex3X[$i]);
            $y_vals = array($vertex1Y[$i], $vertex2Y[$i], $vertex3Y[$i]);
            $z_vals = array($vertex1Z[$i], $vertex2Z[$i], $vertex3Z[$i]);
            if (max($x_vals) > $x_max) {
                $x_max = max($x_vals);
            }
            if (max($y_vals) > $y_max) {
                $y_max = max($y_vals);
            }
            if (max($z_vals) > $z_max) {
                $z_max = max($z_vals);
            }
            if (min($x_vals) < $x_min) {
                $x_min = min($x_vals);
            }
            if (min($y_vals) < $y_min) {
                $y_min = min($y_vals);
            }
            if (min($z_vals) < $z_min) {
                $z_min = min($z_vals);
            }

        }

        $x_dim = $x_max - $x_min;
        $y_dim = $y_max - $y_min;
        $z_dim = $z_max - $z_min;

        return array($x_dim, $y_dim, $z_dim);
        /*
        $volume = $x_dim*$y_dim*$z_dim;

        $raw_cost = 15;
        $tray_cost = $raw_cost;
        $material_cost = $raw_cost*$volume*1.02;
        $support_cost = $raw_cost*2;
        $total = $tray_cost + $material_cost + $support_cost;
        echo "$".number_format($total, 2, '.', ',');
        */

    }

    public function getThumb()
    {

        if (!file_exists($this->filepath)){
            throw new Exception("file $this->filepath not exists");
        }
		$strFile = $this->filepath;
        $strFileName = basename($strFile);

        $MULTIPART_BOUNDARY = '--------------------------'.microtime(true);
        $file_contents = file_get_contents($strFile);

        $content =  "--".$MULTIPART_BOUNDARY."\r\n".
                    "Content-Disposition: form-data; name=\"file\"; filename=\"".$strFileName."\"\r\n".
                    "Content-Type: application/zip\r\n\r\n".
                    $file_contents."\r\n";

        // add some POST fields to the request too: $_POST['foo'] = 'bar'
        // $content .= "--".MULTIPART_BOUNDARY."\r\n".
        //             "Content-Disposition: form-data; name=\"foo\"\r\n\r\n".
        //             "bar\r\n".

        $content .= "--".$MULTIPART_BOUNDARY."--\r\n";

        $context = stream_context_create(array(
            'http' => array(
                  'method' => 'POST',
                  'max_redirects' => '0',
                  'header' => 'Content-Type: multipart/form-data; boundary='.$MULTIPART_BOUNDARY,
                  'content' => $content
            ),
        ));

        $result = @file_get_contents('http://i.materialise.com/Upload?plugin=f3d6382c-8877-4500-94f1-35c4793c3382', false, $context);
        $url = array();
        foreach ($http_response_header as $key => $value) {
            if (strpos($value, 'Location') !== false) {
                preg_match('/(http[\S]+)/', $value, $url);
                break;
            }
        }
        $t = explode('=', $url[0]);
        $modelId = end($t);
		return $modelId;
        // return "http://i.materialise.com/Storage/Models/{$modelId}/Original/preview_small.jpg";
    }

}
