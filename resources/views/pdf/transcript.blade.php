<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Transcript</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 30px;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 2px solid #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .header p {
            font-size: 1rem;
            margin: 0;
        }

        .details {
            margin-bottom: 30px;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details th, .details td {
            padding: 10px;
            text-align: left;
            font-size: 0.9rem;
        }

        .details th {
            width: 20%;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: left;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
        }

        .semester {
            margin-bottom: 20px;
        }

        .semester h4 {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .grades table {
            width: 100%;
            border-collapse: collapse;
        }

        .grades th, .grades td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 0.9rem;
            text-align: left;
        }

        .grades th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer p {
            margin: 5px 0;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            text-align: center;
            width: 48%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
        }

        .signature span {
            display: block;
            margin-top: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Official Transcript</h1>
        <p>Institution Name</p>
        <p>Address Line 1, Address Line 2</p>
        <p>Phone: (123) 456-7890 | Email: info@institution.com</p>
    </div>

    <div class="details">
        <table>
            <tr>
                <th>Student Name:</th>
                <td>{{ $studentProgram->student->first_name }} {{ $studentProgram->student->last_name }}</td>

                <th>Student Reg No.:</th>
                <td>{{ $studentProgram->student->reg_no }}</td>
            </tr>
            <tr>
                <th>Program:</th>
                <td>{{ $studentProgram->program->title }}</td>

                <th>Enrolled On:</th>
                <td>{{ $studentProgram->enrolled_on }}</td>
            </tr>
            <tr>
                <th>Generated On:</th>
                <td>{{ date('l, jS M Y, h:i A') }}</td>

                <th>Status:</th>
                <td>{{ $studentProgram->status }}</td>
            </tr>
        </table>
    </div>

    <div class="transcript">
        <div class="section-title">Academic Record</div>
        @foreach ($studentProgram->student->enrollmentDetails->groupBy('student_enrollment_id') as $index => $semesterEnrollments)
            <div class="semester">
                <h4>Semester {{ $index }}</h4>
                <div class="grades">
                    <table>
                        <thead>
                        <tr>
                            <th>CRN</th>
                            <th>Course</th>
                            <th>Score</th>
                            <th>Grade</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($semesterEnrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->offeredCourse->course->crn }}</td>
                                <td>{{ $enrollment->offeredCourse->course->title }}</td>
                                <td>{{ $enrollment->score ?? 'N/A' }}</td>
                                <td>{{ $enrollment->grade ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <div class="signature">
        <div>
            <div class="signature-line"></div>
            <span>Registrar</span>
        </div>
        <div>
            <div class="signature-line"></div>
            <span>Principal/Dean</span>
        </div>
    </div>

    <div class="footer">
        <p>This transcript is an official document of the institution.</p>
    </div>
</div>
</body>
</html>
