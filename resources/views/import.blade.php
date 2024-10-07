<x-app-layout>
    <x-slot name="header">
        <h2 class="text-x font-semibold text-purple-5 leading-tight">
            {{ __('Import Projects and Tasks/Subtasks') }}
        </h2>
    </x-slot>

    <x-slot name="headerButton">
        <x-link-button href="https://bbitech.work/sample_project_tasks.csv" _target="blank" class="!py-0 !px-2 focus:ring-purple-600 bg-green-800">
            <i class="fa-solid fa-cloud-arrow-down mr-2"></i>
            {{ __('Download Csv Sample') }}
        </x-link-button>
    </x-slot>

    <div class="py-12">

        @if (session('success'))
            <x-success-alert>
                <strong>{{ __(session('success')) }}</strong>
            </x-success-alert>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-fileupload-dnd import-route="{{ route('import.process') }}" />
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 my-6">
            <div class="bg-purple-015 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-purple-5">
                    <h1 class="text-2xl font-bold text-blue-600 mb-4">Instructions for Creating a CSV File for Projects, Tasks, and Subtasks</h1>

                    <p class="mb-4 text-gray-700">Follow these instructions to create a CSV file in Excel that contains project details, tasks, and subtasks. This file will have the following columns:</p>

                    <h2 class="text-xl font-semibold text-blue-500 mb-2">CSV File Structure</h2>
                    <ul class="list-disc list-inside mb-4 text-gray-700">
                        <li><strong>Title:</strong> The title of the project, task, or subtask. Should be a descriptive name.</li>
                        <li><strong>Project:</strong> The row number of the project associated with the task. Leave this blank for project rows.</li>
                        <li><strong>Parent:</strong> The row number of the parent task for a subtask. Leave this blank for projects and tasks.</li>
                        <li><strong>Status:</strong> The current status of the project, task, or subtask. Use the following values:
                            <ul class="list-disc list-inside">
                                <li>For projects: <code>Initiation, Planning, Analysis, Design, Development, Testing, Release, Support, Evaluation, Retirement</code></li>
                                <li>For tasks and subtasks: <code>NotAssigned, Assigned, NotStarted, InProgress, InReview, Approved, Rejected, Cancelled, Closed</code></li>
                            </ul>
                        </li>
                        <li><strong>Priority:</strong> The priority level of the task or subtask. Use one of the following values:
                            <ul class="list-disc list-inside">
                                <li><code>Optional, Low, Medium, High, Critical</code></li>
                            </ul>
                        </li>
                        <li><strong>Deadline:</strong> The deadline for the project, task, or subtask in any valid date-time format.</li>
                        <li><strong>Details:</strong> A brief description of the project, task, or subtask. You can use Lorem Ipsum or relevant details. (<strong>Min: 10 words</strong>)</li>
                    </ul>

                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                        <p class="font-bold">N.B:</p>
                        <p>
                            In the <strong>Project</strong> and <strong>Parent</strong> columns, you can enter existing project IDs if applicable.
                            If you provide an existing project ID, the corresponding tasks and subtasks will be imported to their relevant project or parent task.
                        </p>
                    </div>

                    <h2 class="text-xl font-semibold text-blue-500 mb-2">Example of Data Entry</h2>
                    <p class="mb-2 text-gray-700">Here is a simple example of how your data should look in Excel:</p>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">1</th>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">Title</th>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">Project</th>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">Parent</th>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">Status</th>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">Priority</th>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">Deadline</th>
                                    <th class="border border-gray-300 p-2 text-left text-gray-800">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 p-2 text-gray-700">2</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Website Development</td>
                                    <td class="border border-gray-300 p-2 text-gray-700"></td>
                                    <td class="border border-gray-300 p-2 text-gray-700"></td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Development</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">High</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">2024-05-01 12:00:00</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Create a responsive website.</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 p-2 text-gray-700">3</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Design Layout</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">2</td>
                                    <td class="border border-gray-300 p-2 text-gray-700"></td>
                                    <td class="border border-gray-300 p-2 text-gray-700">InProgress</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Medium</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">2024-03-15 12:00:00</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Design the layout of the website.</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 p-2 text-gray-700">4</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Review Mockups</td>
                                    <td class="border border-gray-300 p-2 text-gray-700"></td>
                                    <td class="border border-gray-300 p-2 text-gray-700">3</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">NotStarted</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Low</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">2024-03-20 12:00:00</td>
                                    <td class="border border-gray-300 p-2 text-gray-700">Review the mockups created by the designer.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
