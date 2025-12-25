<?php
include 'connection/connection.php';
session_start();

isset($_SESSION['AuthUser']) ? $AuthUser = $_SESSION['AuthUser'] : $AuthUser = [];
$isAuthenticated = !empty($AuthUser);

isset($_SESSION['error']) && print_r($_SESSION['error']);
isset($_SESSION['success']) && print_r($_SESSION['success']);

$total_expenses = isset($_SESSION['TOTAL_EXPENSES']) ? $_SESSION['TOTAL_EXPENSES'] : 0;
$total_incomes = isset($_SESSION['TOTAL_INCOMES']) ? $_SESSION['TOTAL_INCOMES'] : 0;
$incomes = isset($_SESSION['INCOMES']) ? $_SESSION['INCOMES'] : [];
$categories = isset($_SESSION['CATEGORIES']) ? $_SESSION['CATEGORIES'] : [];
$expenses = isset($_SESSION['EXPENCES']) ? $_SESSION['EXPENCES'] : [];
$history = isset($_SESSION['History']) ? $_SESSION['History'] : [];

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
        <!-- Header with Auth Buttons -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Financial Dashboard</h1>

            <!-- Auth Buttons -->
            <div class="flex gap-3">
                <?php if ($isAuthenticated): ?>

                    <button id="logout" class="px-5 py-2.5 rounded-lg bg-gray-500 hover:bg-gray-600 text-white font-semibold transition duration-200 shadow-md">
                        Logout
                    </button>
                    <button class="create_transaction px-5 py-2.5 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition duration-200 shadow-md">
                        + New Transaction
                    </button>

                    <button id="create_card" class="px-5 py-2.5 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white font-semibold transition duration-200 shadow-md">
                        + New card
                    </button>
                    <button id="create_category" class="px-5 py-2.5 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white font-semibold transition duration-200 shadow-md">
                        + New category
                    </button>


                <?php else: ?>
                    <button id="login" class="px-5 py-2.5 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold transition duration-200 shadow-md">
                        Login
                    </button>
                    <!--onclick=" window.location.href='controllers/user_controller/rejester.php'" -->
                    <button id="Register" class="px-5 py-2.5 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold transition duration-200 shadow-md">
                        Register
                    </button>

                <?php endif; ?>

            </div>
        </div>

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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
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
                        if (empty($expenses[0]["message"])) {
                            foreach ($expenses as $expense) {
                                echo '<tr class="hover:bg-red-50">';
                                echo "<td class='border border-red-500 px-3 py-2'><span class='font-medium'>" . number_format($expense['amount'], 2) . " DH</span></td>";
                                echo "<td class='border border-red-500 px-3 py-2'><span class='text-sm text-gray-700'>" . htmlspecialchars($expense['description']) . "</span></td>";
                                echo "<td class='border border-red-500 px-3 py-2'><span class='text-sm'>" . (new DateTime($expense['created_at']))->format('Y/m/d') . "</span></td>";
                                echo "<td class='p-2 border border-red-500'>
                                    <span name='" . $expense['id'] . "' class='delete_expense text-red-700 text-sm font-medium cursor-pointer hover:text-red-900 mr-2'>Delete</span>
                                    <span name='" . $expense['id'] . "' class='update_expense text-blue-700 text-sm font-medium cursor-pointer hover:text-blue-900'>Update</span>
                                  </td>";
                                echo '</tr>';
                            }
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
                        if (empty($incomes[0]["message"])) {
                            foreach ($incomes as $income) {
                                echo '<tr class="hover:bg-green-50">';
                                echo "<td class='border border-green-500 px-3 py-2'><span class='font-medium'>" . number_format($income['amount'], 2) . " DH</span></td>";
                                echo "<td class='border border-green-500 px-3 py-2'><span class='text-sm text-gray-700'>" . htmlspecialchars($income['description']) . "</span></td>";
                                echo "<td class='border border-green-500 px-3 py-2'><span class='text-sm'>" . (new DateTime($income['created_at']))->format('Y/m/d') . "</span></td>";
                                echo "<td class='p-2 border border-green-500'>
                                    <span name='" . $income['id'] . "' class='delete_income text-red-700 text-sm font-medium cursor-pointer hover:text-red-900 mr-2'>Delete</span>
                                    <span name='" . $income['id'] . "' class='update_income text-blue-700 text-sm font-medium cursor-pointer hover:text-blue-900'>Update</span>
                                  </td>";
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- History Table - Full Width -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="border border-yellow-500 text-center bg-yellow-300 py-2 font-bold text-gray-700" colspan="6">TRANSACTION HISTORY</th>
                    </tr>
                    <tr class="bg-yellow-50">
                        <th class="border border-yellow-500 px-3 py-2 text-left text-sm font-semibold">Type</th>
                        <th class="border border-yellow-500 px-3 py-2 text-left text-sm font-semibold">Amount</th>
                        <th class="border border-yellow-500 px-3 py-2 text-left text-sm font-semibold">Sender</th>
                        <th class="border border-yellow-500 px-3 py-2 text-left text-sm font-semibold">Receiver</th>
                        <th class="border border-yellow-500 px-3 py-2 text-left text-sm font-semibold">Date</th>
                        <th class="border border-yellow-500 px-3 py-2 text-left text-sm font-semibold">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($history) && empty($history[0]["message"])) {
                        foreach ($history as $transaction) {
                            echo '<tr class="hover:bg-yellow-50">';

                            // Transaction Type with badge
                            $typeBadge = '';
                            $descriptionText = '';
                            if ($transaction['transaction_type'] == 'sender') {
                                $typeBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Sent</span>';
                                $descriptionText = 'You sent money to ' . htmlspecialchars($transaction['receiver_name']);
                            } else {
                                $typeBadge = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Received</span>';
                                $descriptionText = 'You received money from ' . htmlspecialchars($transaction['sender_name']);
                            }
                            echo "<td class='border border-yellow-500 px-3 py-2'>" . $typeBadge . "</td>";

                            // Amount
                            echo "<td class='border border-yellow-500 px-3 py-2'><span class='font-medium'>" . number_format($transaction['amount'], 2) . " DH</span></td>";

                            // Sender info
                            echo "<td class='border border-yellow-500 px-3 py-2'>
                                <div class='text-sm font-medium'>" . htmlspecialchars($transaction['sender_name']) . "</div>
                                <div class='text-xs text-gray-500'>" . htmlspecialchars($transaction['sender_email']) . "</div>
                            </td>";

                            // Receiver info
                            echo "<td class='border border-yellow-500 px-3 py-2'>
                                <div class='text-sm font-medium'>" . htmlspecialchars($transaction['receiver_name']) . "</div>
                                <div class='text-xs text-gray-500'>" . htmlspecialchars($transaction['receiver_email']) . "</div>
                            </td>";

                            // Date
                            echo "<td class='border border-yellow-500 px-3 py-2'><span class='text-sm'>" . (new DateTime($transaction['created_at']))->format('Y/m/d H:i') . "</span></td>";

                            // Description column
                            echo "<td class='border border-yellow-500 px-3 py-2'>
                                <div class='text-sm text-gray-700'>" . $descriptionText . "</div>
                                <div class='text-xs text-gray-500 mt-1'>
                                    message : " . (isset($transaction['description']) && trim($transaction['description']) ? $transaction['description'] : 'no description') . "
                                </div>
                            </td>";

                            echo '</tr>';
                        }
                    } else {
                        echo '<tr>';
                        echo '<td colspan="6" class="border border-yellow-500 px-3 py-8 text-center text-gray-500">No transaction history available</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>



    <script>
        const totalIncomes = <?php echo $total_incomes; ?>;
        const totalExpenses = <?php echo $total_expenses; ?>;
        const totalBalance = totalIncomes - totalExpenses;
        const AuthUser = <?php echo json_encode($AuthUser); ?>;
        //const cards = <?php //echo json_encode($cards); ?>;
        const categories = <?php echo json_encode($categories); ?>;
        const isAuthenticated = <?php echo $isAuthenticated ? 'true' : 'false'; ?>;
    </script>

    <script src="main.js"></script>
</body>