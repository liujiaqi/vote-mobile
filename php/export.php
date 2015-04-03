<?php
    session_start();
    if(!isset($_SESSION['admin'])) die("请您先登录！");
    
    require_once('Classes/PHPExcel.php');
	require_once('db.php');
    
    $result = query("select * from parameter");
    $parameter = mysql_fetch_array($result);
    
        
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("SDU Online投票管理系统")
                                 ->setLastModifiedBy("SDU Online投票管理系统")
                                 ->setTitle($parameter['title']."投票结果")
                                 ->setSubject($parameter['title']."投票结果")
                                 ->setDescription($parameter['title']." 投票结果, Gererated by SDU Online投票管理系统, Powered by SDU Online")
                                 ->setKeywords($parameter['title']." 投票结果")
                                 ->setCategory("投票结果");


    $objPHPSheet = $objPHPExcel->getSheet(0);
    $objPHPSheet ->mergeCells('A1:B1');
    $objPHPSheet ->setTitle('投票结果')
                          ->setCellValue('A1', $parameter['title']."投票结果")
                          ->setCellValue('A2', '候选人姓名')
                          ->setCellValue('B2', '得票数');
                          
    
    $result = query("select name, poll from candidate where state = 1 order by poll desc");
    $i = 3;
    while($row = mysql_fetch_array($result)){
        $objPHPSheet ->setCellValue("A$i", $row["name"])
                              ->setCellValue("B$i", $row["poll"]);
        $i++;
    }
    $i--;
    $styleThinBlackBorderOutline = array(
        'borders' => array (
           'allborders' => array (
              'style' => PHPExcel_Style_Border::BORDER_THIN, 
              'color' => array ('argb' => 'FF000000')
          ),
       ),
    );
    $objPHPSheet ->getStyle( "A1:B$i")->applyFromArray($styleThinBlackBorderOutline);
 
    $objPHPSheet ->getStyle('A1')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('A2')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('B2')->getFont()->setBold(true);
    $objPHPSheet ->getColumnDimension('A')->setWidth(15);
    $objPHPSheet ->getColumnDimension('B')->setWidth(8);
                          
                         
    $objPHPExcel ->createSheet();
    $objPHPSheet = $objPHPExcel->getSheet(1);
    
    $result = query("select count(*) as nvote, (select count(*) from candidate where state =1) as ncandi, (select count(distinct uid) from vote where state =1) as nvoteu, (select count(*) from `user` where state =1)  as nuser  from vote  where state =1");
    $row = mysql_fetch_array($result);
    
    $objPHPSheet ->setTitle('投票数据')
                          ->setCellValue('A1', '开始时间')
                          ->setCellValue('B1', $parameter['begintime'])
                          ->setCellValue('C1', '结束时间')
                          ->setCellValue('D1', $parameter['endtime'])
                          ->setCellValue('A2', '候选人总数')
                          ->setCellValue('B2', $row['ncandi'])
                          ->setCellValue('C2', '投票人数')
                          ->setCellValue('D2', $row['nvoteu'].'/'.$row['nuser'])
                          ->setCellValue('A3', '每人投票数')
                          ->setCellValue('B3', $parameter['total'])
                          ->setCellValue('C3', '投票总数')
                          ->setCellValue('D3', $row['nvote']);
                          
    $objPHPSheet ->getStyle( 'A1:D3')->applyFromArray($styleThinBlackBorderOutline);
    $objPHPSheet ->getStyle('A1')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('C1')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('A2')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('C2')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('A3')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('C3')->getFont()->setBold(true);
    
    
    $objPHPSheet ->setTitle('投票数据')
                          ->setCellValue('A5', 'UID')
                          ->setCellValue('B5', '投票人')
                          ->setCellValue('C5', 'CID')
                          ->setCellValue('D5', '候选人')
                          ->setCellValue('E5', '投票时间')
                          ->setCellValue('F5', '投票IP');
    $objPHPSheet ->getStyle('A5')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('B5')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('C5')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('D5')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('E5')->getFont()->setBold(true);
    $objPHPSheet ->getStyle('F5')->getFont()->setBold(true);

    $result = query("select uid,realname, cid,candidate.name,`time`,vote.ip from vote left join `user` on vote.uid = `user`.id  left join candidate on vote.cid = candidate.id where vote.state =1");
    $i = 6;
    while($row = mysql_fetch_array($result)){
        $objPHPSheet ->setCellValue("A$i", $row["uid"])
                              ->setCellValue("B$i", $row["realname"])
                              ->setCellValue("C$i", $row["cid"])
                              ->setCellValue("D$i", $row["name"])
                              ->setCellValue("E$i", $row["time"])
                              ->setCellValue("F$i", $row["ip"]);
        $i++;
    }
    $i--;
    $objPHPSheet ->getStyle("A5:F$i")->applyFromArray($styleThinBlackBorderOutline);
    $objPHPSheet ->getColumnDimension('A')->setWidth(15);
    $objPHPSheet ->getColumnDimension('B')->setWidth(20);
    $objPHPSheet ->getColumnDimension('C')->setWidth(10);
    $objPHPSheet ->getColumnDimension('D')->setWidth(20);
    $objPHPSheet ->getColumnDimension('E')->setWidth(20);
    $objPHPSheet ->getColumnDimension('F')->setWidth(15);
   
   
    $objPHPExcel->setActiveSheetIndex(0);
    
   

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$parameter['title'].'投票结果.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
?>