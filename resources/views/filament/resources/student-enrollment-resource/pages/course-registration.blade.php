<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Available Courses</h2>

    @if($semester = $this->getSemester())
        <p>Current Semester: {{ $semester->type }} {{ $semester->year }}</p>

        <table class="table-auto w-full mt-4">
            <thead>
            <tr>
                <th class="px-4 py-2">Course</th>
                <th class="px-4 py-2">Instructor</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($this->getOfferedCourses() as $course)
                <tr>
                    <td class="border px-4 py-2">{{ $course->programCourse->course->title }}</td>
                    <td class="border px-4 py-2">{{ $course->instructor->name }}</td>
                    <td class="border px-4 py-2">
                        <form method="POST" action="{{ route('enroll.course', $course->id) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">
                                Add
                            </button>
                        </form>
                        <form method="POST" action="{{ route('drop.course', $course->id) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">
                                Drop
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No courses available.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    @else
        <p>Registration is currently closed.</p>
    @endif
</x-filament::page>
