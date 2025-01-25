<?php

namespace App\Http\Controllers;

use App\Models\CourseTiming;
use App\Models\OfferedCourse;
use App\Models\FeeVoucher;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentEnrollmentDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Carbon\Carbon;


class StudentController extends Controller
{
    public function downloadFeeVoucher(FeeVoucher $voucher)
    {
        $pdf = Pdf::loadView('pdf.fee-voucher', ['voucher' => $voucher]);

        // Return the PDF as a download
        return $pdf->download("fee-voucher-{$voucher->id}.pdf");
    }

    public function downloadAttendanceSheet($schedule_id)
    {
        $data = $this->getSheetData($schedule_id);

        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $this->setHeader($spreadsheet->getActiveSheet(), $data);

        $sheet->setCellValue('A7', 'Alias Description')
            ->getStyle('A7')->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('B7', 'A=Absence, L=Late, S=Suspension, H=Holiday, I=Illness, E=Early Departure')
            ->mergeCells('B7:P7');

        // Table Heading for Student List
        $sheet->setCellValue('A9', 'Student Name')
            ->getStyle('A9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('B9', 'Dates')
            ->getStyle('B9')->getFont()->setBold(true)->setSize(11);

        // Generate Dates Based on Schedule
        $scheduleDates = [];
        $currentDate = $data['startDate'];
        while ($currentDate <= $data['endDate']) {
            if (strtolower($currentDate->format('l')) === strtolower($data['class']->day)) {
                $scheduleDates[] = $currentDate->format('d/m');
            }
            $currentDate = $currentDate->addDay();
        }

        // Populate Dates in Header (compact columns for date format only)
        $index = 1;
        $row = 9; // Start from row 8 for date columns
        foreach ($scheduleDates as $date) {
            $col = Coordinate::stringFromColumnIndex($index + 1); // Convert column number to letter
            $sheet->setCellValue("$col$row", $date)
                ->getStyle("$col$row")->getFont()->setBold(true)->setSize(10);
            $sheet->getColumnDimension($col)->setWidth(5); // Apply compact width to date columns
            $index++;
        }
        $col = Coordinate::stringFromColumnIndex($index + 1);
        $sheet->setCellValue("$col$row", 'TTL%')
            ->getStyle("$col$row")->getFont()->setBold(true)->setSize(10);
        $index++;
        $col = Coordinate::stringFromColumnIndex($index + 1);
        $sheet->setCellValue("$col$row", 'Remarks')
            ->getStyle("$col$row")->getFont()->setBold(true)->setSize(10);

        // Adjust the width of all other columns to minimum 15
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            if (!in_array($col, range('B', Coordinate::stringFromColumnIndex($index)))) { // Skip date columns
                $sheet->getColumnDimension($col)->setWidth(15); // Minimum width of 15 for other columns
            }
        }

        // Fill Student Names and Empty Attendance (For now)
        $row = 10; // Start from row 9 for student data
        foreach ($data['students'] as $student) {
            $sheet->setCellValue('A' . $row, $student->first_name . ' ' . $student->last_name);
            for ($index = 2; $index < count($scheduleDates) + 2; $index++) {
                $col = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue("$col$row", ''); // Empty for attendance marking
            }
            $row++;
        }

        // Style Table (Grid Lines and Centering)
        $sheet->getStyle('A9:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('A9:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Highlight Header Row
        $sheet->getStyle('A9:' . $sheet->getHighestColumn() . '9')
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setRGB('FFFFFF');

        $sheet->getStyle('A9:' . $sheet->getHighestColumn() . '9')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('4CAF50');

        // Footer Section (optional)
        $row += 5;
        $sheet->setCellValue('A' . $row, 'Generated on: ' . now()->format('Y-m-d H:i:s'))
            ->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(10);
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)  // Set landscape orientation
            ->setFitToPage(true)  // Fit to one page
            ->setFitToWidth(1)    // Fit width to 1 page
            ->setFitToHeight(0);  // No restriction on height (allow it to extend beyond 1 page if needed)

        // Save as Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'attendance_sheet_' . $data['class']->offeredCourse->course->crn . '_' .
            $data['semester']->year . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
    }

    public function downloadGradeSheet($offered_course_id)
    {
        $schedule_id = CourseTiming::where('offered_course_id', $offered_course_id)->first()->id;
        $data = $this->getSheetData($schedule_id);

        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
        $sheet = $this->setHeader($spreadsheet->getActiveSheet(), $data);

        $enrollments = StudentEnrollmentDetail::with('student')
            ->where('offered_course_id', $offered_course_id)->get();

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(14);  // For Student ID
        $sheet->getColumnDimension('F')->setWidth(3);
        $sheet->getColumnDimension('G')->setWidth(4);
        $sheet->getColumnDimension('H')->setWidth(3);
        $sheet->getColumnDimension('I')->setWidth(4);
        $sheet->getColumnDimension('J')->setWidth(35);

        // Table Headings for Student List
        $sheet->setCellValue('A9', 'Student ID')
            ->getStyle('A9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('B9', 'Student Name')->mergeCells('B9:C9')
            ->getStyle('B9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D9', 'Score')
            ->getStyle('D9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('E9', 'Grade')
            ->getStyle('E9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('F9', 'Comments')->mergeCells('F9:J9')
            ->getStyle('F9')->getFont()->setBold(true)->setSize(11);

        // Fill Student Names and Empty Attendance (For now)
        $row = 10; // Start from row 9 for student data
        foreach ($enrollments as $enrollment) {
            $sheet->setCellValue('A' . $row, $enrollment->student->reg_no);
            $sheet->setCellValue('B' . $row, $enrollment->student->first_name . ' ' .
                $enrollment->student->last_name)->mergeCells('B' . $row . ':C' . $row);
            $sheet->setCellValue('D' . $row, $enrollment->score);
            $sheet->setCellValue('E' . $row, $enrollment->grade);
            $sheet->setCellValue('F' . $row, $enrollment->comments)
                ->mergeCells('F' . $row . ':J' . $row);
            $row++;
        }

        // Style Table (Grid Lines and Centering)
        $sheet->getStyle('A9:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('A9:J' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Highlight Header Row
        $sheet->getStyle('A9:J9')
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setRGB('FFFFFF');

        $sheet->getStyle('A9:J9')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('4CAF50');

        // Footer Section (optional)
        $row += 5;
        $sheet->setCellValue('A' . $row, 'Generated on: ' . now()->format('Y-m-d H:i:s'))
            ->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(10);

        $sheet->getPageSetup()
            ->setFitToPage(true)  // Fit to one page
            ->setFitToWidth(1)    // Fit width to 1 page
            ->setFitToHeight(0);  // No restriction on height (allow it to extend beyond 1 page if needed)

        // Save as Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'grade_sheet_' . $data['class']->offeredCourse->course->crn . '_' .
            $data['semester']->year . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
    }

    protected function getSheetData($schedule_id)
    {
        $class = CourseTiming::with(['offeredCourse.course', 'offeredCourse.instructor'])
            ->findOrFail($schedule_id);

        // Get students enrolled in the offered course, excluding "Dropped" status
        $students = Student::whereHas('enrollments.enrollmentDetails',
            function ($query) use ($class) {
                $query->where('offered_course_id', $class->offered_course_id)
                    ->where('status', '!=', 'Dropped');
            })->with('enrollments')->get();

        $semester_id = OfferedCourse::find($class->offered_course_id)->semester_id;
        $semester = Semester::findOrFail($semester_id);

        $startDate = Carbon::parse($semester->start_date);
        $endDate = Carbon::parse($semester->end_date);

        return compact('class', 'students', 'semester', 'startDate', 'endDate');
    }

    protected function setHeader($sheet, $data)
    {
        // Header Section
        $logoPath = storage_path('app/public/img/logo.jpeg');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo')
            ->setDescription('Logo')
            ->setPath($logoPath)
            ->setHeight(50)
            ->setCoordinates('A1');
        $drawing->setWorksheet($sheet);

        // Title Section
        $sheet->setCellValue('B1', 'Attendance Record')
            ->mergeCells('B1:J1')
            ->getStyle('B1')
            ->getFont()
            ->setSize(18)
            ->setBold(true);
        $sheet->getStyle('B1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Course and Semester Details (use merged cells for clarity)
        $sheet->setCellValue('A4', 'Course CRN')
            ->getStyle('A4')->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('B4', $data['class']->offeredCourse->course->crn)
            ->mergeCells('B4:E4');

        $sheet->setCellValue('F4', 'Course Title')->mergeCells('F4:I4')
            ->getStyle('F4')->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('J4', $data['class']->offeredCourse->course->title)
            ->mergeCells('J4:N4');

        $sheet->setCellValue('A5', 'Instructor')
            ->getStyle('A5')->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('B5', $data['class']->offeredCourse->instructor->full_name)
            ->mergeCells('B5:E5');
        $sheet->setCellValue('F5', 'Time Schedule')->mergeCells('F5:I5')
            ->getStyle('F5')->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('J5', $data['class']->day . ' ' .
            date('h:i A', strtotime($data['class']->start_time)) . ' - ' .
            date('h:i A', strtotime($data['class']->end_time)))->mergeCells('J5:N5');

        $sheet->setCellValue('A6', 'Semester')
            ->getStyle('A6')->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('B6', $data['semester']->type . ' ' . $data['semester']->year)
            ->mergeCells('B6:E6');
        $sheet->setCellValue('F6', 'Period')->mergeCells('F6:I6')
            ->getStyle('F6')->getFont()->setBold(true)->setSize(10);
        $sheet->setCellValue('J6', $data['startDate']->format('jS M Y') . ' to ' .
            $data['endDate']->format('jS M Y'))->mergeCells('J6:N6');


        return $sheet;
    }

}
