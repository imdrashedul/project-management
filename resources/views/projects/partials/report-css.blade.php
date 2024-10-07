<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        line-height: 1.6;
        color: #333;
    }

    .container {
        width: 100%;
        padding: 20px;
    }

    .project-card {
        background-color: #f9f9f9;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .project-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .project-title {
        font-size: 24px;
        font-weight: bold;
        color: #4a4a4a;
    }

    .project-meta {
        font-size: 14px;
        color: #888;
    }

    .project-details {
        margin-top: 10px;
        font-size: 14px;
    }

    .task-table,
    .subtask-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .task-table th,
    .task-table td,
    .subtask-table th,
    .subtask-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .task-table th,
    .subtask-table th {
        background-color: #f2f2f2;
        color: #333;
    }

    .task-table tbody tr:nth-child(even),
    .subtask-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .card-stats {
        margin-top: 15px;
        width: 100%
    }

    .stat-box {
        background-color: #e7f3fe;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
    }

    .stat-title {
        font-weight: bold;
        font-size: 16px;
    }

    .stat-value {
        font-size: 20px;
        color: #007bff;
    }

    .label {
        font-weight: bold;
        color: #555;
    }
</style>
