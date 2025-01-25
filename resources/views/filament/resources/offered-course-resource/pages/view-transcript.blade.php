<x-filament::page>
    <div class="space-y-6">
        <!-- Header Table for Offered Course Details -->
        <div class="card p-6">
            <h2 class="text-xl font-bold mb-4">Course Details</h2>
            <table class="min-w-full border-collapse border border-gray-200" style="width:100%">
                <tbody>
                <tr>
                    <td class="border border-gray-300 px-4 py-2 font-medium">Course Code</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $course->course->crn }}</td>
                    <td class="border border-gray-300 px-4 py-2 font-medium">Course Name</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $course->course->title }}</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-4 py-2 font-medium">Semester</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $course->semester->type }} {{ $course->semester->year }}</td>
                    <td class="border border-gray-300 px-4 py-2 font-medium">Duration</td>
                    <td class="border border-gray-300 px-4 py-2">
                        {{ date('jS M Y', strtotime($course->semester->start_date)) }}
                        - {{ date('jS M Y', strtotime($course->semester->end_date)) }}</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-4 py-2 font-medium">Instructor</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $course->instructor->name ?? 'N/A' }}</td>
                    <td class="border border-gray-300 px-4 py-2 font-medium">Status</td>
                    <td class="border border-gray-300 px-4 py-2">{{ ucfirst($course->status) }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Table for Students and Grades -->
        <div class="card p-6">
            <h2 class="text-xl font-bold mb-4">Enrolled Students</h2>
            <table class="min-w-full border-collapse border border-gray-200" style="width:100%">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">Student ID</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Score</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Grade</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Comments</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->student->reg_no }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->student->first_name }} {{ $student->student->last_name }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->score ?? 'N/A' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->grade ?? 'N/A' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->comments ?? 'N/A' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
