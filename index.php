<?php
include 'connection/connection.php';
include 'expenses/index.php';
include 'incomes/index.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Financial Dashboard</h1>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Incomes Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Incomes</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">
                            <?php echo number_format($total_incomes, 2); ?> DH
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Expenses Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Expenses</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">
                            <?php echo number_format($total_expenses, 2); ?> DH
                        </p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Balance Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Balance</p>
                        <p class="text-3xl font-bold <?php echo ($total_incomes - $total_expenses) >= 0 ? 'text-blue-600' : 'text-red-600'; ?> mt-2">
                            <?php echo number_format($total_incomes - $total_expenses, 2); ?> DH
                        </p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Bar Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Financial Comparison</h2>
                <canvas id="financeChart" class="w-full" style="max-height: 300px;"></canvas>
            </div>

            <!-- Pie Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Income vs Expenses</h2>
                <canvas id="pieChart" class="w-full" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Action Buttons -->
        <section class="mb-6 flex gap-4">
            <button class="create_income px-6 py-3 rounded-lg bg-green-500 hover:bg-green-600 text-white font-bold transition duration-200 shadow-md">
                + Create Income
            </button>
            <button class="create_expence px-6 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold transition duration-200 shadow-md">
                + Create Expense
            </button>
        </section>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Expenses Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="border border-red-500 text-center bg-red-300 py-2 font-bold text-gray-700" colspan="4">EXPENSES</th>
                        </tr>
                        <tr class="bg-red-50">
                            <th class="border border-red-500 px-3 py-2 text-left text-sm font-semibold">Montant</th>
                            <th class="border border-red-500 px-3 py-2 text-left text-sm font-semibold">Description</th>
                            <th class="border border-red-500 px-3 py-2 text-left text-sm font-semibold">Created At</th>
                            <th class="border border-red-500 px-3 py-2 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($expenses as $expense) {
                            echo '<tr class="hover:bg-red-50">';
                            echo "<td class='border border-red-500 px-3 py-2'><span class='font-medium'>" . number_format($expense['montant'], 2) . " DH</span></td>";
                            echo "<td class='border border-red-500 px-3 py-2'><span class='text-sm text-gray-700'>" . htmlspecialchars($expense['description']) . "</span></td>";
                            echo "<td class='border border-red-500 px-3 py-2'><span class='text-sm'>" . (new DateTime($expense['created_at']))->format('Y/m/d') . "</span></td>";
                            echo "<td class='p-2 border border-red-500'>
                                    <span name='" . $expense['id'] . "' class='delete_expense text-red-700 text-sm font-medium cursor-pointer hover:text-red-900 mr-2'>Delete</span>
                                    <span name='" . $expense['id'] . "' class='update_expense text-blue-700 text-sm font-medium cursor-pointer hover:text-blue-900'>Update</span>
                                  </td>";
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Incomes Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="border border-green-500 text-center bg-green-300 py-2 font-bold text-gray-700" colspan="4">INCOMES</th>
                        </tr>
                        <tr class="bg-green-50">
                            <th class="border border-green-500 px-3 py-2 text-left text-sm font-semibold">Montant</th>
                            <th class="border border-green-500 px-3 py-2 text-left text-sm font-semibold">Description</th>
                            <th class="border border-green-500 px-3 py-2 text-left text-sm font-semibold">Created At</th>
                            <th class="border border-green-500 px-3 py-2 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($incomes as $income) {
                            echo '<tr class="hover:bg-green-50">';
                            echo "<td class='border border-green-500 px-3 py-2'><span class='font-medium'>" . number_format($income['montant'], 2) . " DH</span></td>";
                            echo "<td class='border border-green-500 px-3 py-2'><span class='text-sm text-gray-700'>" . htmlspecialchars($income['description']) . "</span></td>";
                            echo "<td class='border border-green-500 px-3 py-2'><span class='text-sm'>" . (new DateTime($income['created_at']))->format('Y/m/d') . "</span></td>";
                            echo "<td class='p-2 border border-green-500'>
                                    <span name='" . $income['id'] . "' class='delete_income text-red-700 text-sm font-medium cursor-pointer hover:text-red-900 mr-2'>Delete</span>
                                    <span name='" . $income['id'] . "' class='update_income text-blue-700 text-sm font-medium cursor-pointer hover:text-blue-900'>Update</span>
                                  </td>";
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const totalIncomes = <?php echo $total_incomes; ?>;
        const totalExpenses = <?php echo $total_expenses; ?>;
        const totalBalance = totalIncomes - totalExpenses;
    </script>

    <script src="main.js"></script>
</body>

</html>