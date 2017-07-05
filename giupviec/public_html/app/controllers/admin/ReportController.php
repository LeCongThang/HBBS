<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 1/4/2017
 * Time: 2:45 PM
 */

namespace app\controllers\admin;


use app\helpers\ArrayHelper;
use app\helpers\DateHelper;
use app\models\Program;
use app\models\Register;
use app\models\RegisterCourse;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet;

class ReportController extends ControllerBase
{
    public function indexAction()
    {
        $name = $this->getRequest()->get('name');
        $program_id = $this->getRequest()->get('program_id');
        $page = $this->getRequest()->get('page', 1);

        if ($this->getRequest()->get('excel')) {
            $this->excel($page, $name, $program_id);
            return;
        }

        list($pages, $registers) = RegisterCourse::model()->getReport($page, $name, $program_id);
        $courses = Program::model()->getAllName($program_id);
        $programs = Program::model()->getAllSelect();

        $this->view->render('admin/report/index', [
            'registers' => $registers,
            'programs' => $programs,
            'courses' => $courses,
            'pages' => $pages,
            'name' => $name,
            'program_id' => $program_id,
        ]);

    }

    public function excel($page, $name, $program_id)
    {
        list($pages, $registers) = RegisterCourse::model()->getReport($page, $name, $program_id);
        $courses = Program::model()->getAllName($program_id);
        if (empty($registers)) {
            return;
        }
        set_time_limit(0);
        $objPHPExcel = new PHPExcel();
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Họ và Tên học viên');

        $sheet->mergeCells("A1:B1");

        $calculatedWidth = $sheet->getColumnDimension("A")->getWidth();
        $sheet->getColumnDimension("A")->setWidth(20);
        $index = 2;

        foreach ($courses as $item) {
            $sheet->setCellValueByColumnAndRow($index++, 1, $item['name']);
        }
        $rowIndex = 2;

        foreach ($registers as  $register) {
            $row1 = "A" . $rowIndex;
            $row2 = "A" . ($rowIndex + 1);
            $row3 = $rowIndex + 2;
            $colIndex = 1;
            $sheet->setCellValue($row1, $register['name']);
            $sheet->mergeCells("$row1:$row2");
            $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, "Số tiết");
            $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex + 1, "GV");
            $sheet->setCellValueByColumnAndRow(0, $row3, "Đã học");
            $sheet->mergeCells("A" . $row3 . ":B" . $row3);
            $_courses = $register['courses'];
            foreach ($courses as $program_id => $item) {
                if (isset($_courses[$item['id']])) {
                    $colIndex++;
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $_courses[$item['id']]['lesson']);
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex + 1, $_courses[$item['id']]['teacher']);
                    if ($_courses[$item['id']]['complete']) {
                        $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex + 2, '✓');
                    }
                } else {
                    $colIndex++;
                }
            }
            $rowIndex += 3;
        }

        $alpha = $this->num2alpha($colIndex);
        $maxRow = $rowIndex - 1;
        $styleRow = $sheet->getStyle('A1:' . $alpha . '1');
        $styleRow->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('dce6f1');
        $styleRow->getAlignment()
            ->setWrapText(true)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $styleRow->getFont()->setBold(true);

        $styleCol = $sheet->getStyle('A1:B' . $maxRow);
        $styleCol->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('c5d9f1');
        $styleCol->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $allCell = $sheet->getStyle('A1:' . $alpha . $maxRow);

        $allCell->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $allCell->getFont()->setSize(10);
        $allCell->applyFromArray($styleArray);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="report-' . date('d-m-Y').'_page_'. $page . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save('php://output');

    }



    private function num2alpha($n)
    {
        for ($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n % 26 + 0x41) . $r;
        return $r;
    }
}