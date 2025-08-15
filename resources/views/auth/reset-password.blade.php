<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Reset Password</h2>

        <form id="resetForm" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="token" class="block text-sm font-medium text-gray-700">Reset Token</label>
                <input type="text" id="token" name="token" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Reset Password
            </button>
        </form>

        <div id="message" class="mt-4 text-center hidden"></div>
    </div>

    <script>
        document.getElementById('resetForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('/api/auth/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                const messageDiv = document.getElementById('message');

                if (response.ok) {
                    messageDiv.className = 'mt-4 text-center text-green-600';
                    messageDiv.textContent = result.message;
                } else {
                    messageDiv.className = 'mt-4 text-center text-red-600';
                    messageDiv.textContent = result.error || 'An error occurred';
                }

                messageDiv.classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                const messageDiv = document.getElementById('message');
                messageDiv.className = 'mt-4 text-center text-red-600';
                messageDiv.textContent = 'An error occurred. Please try again.';
                messageDiv.classList.remove('hidden');
            }
        });

        // Auto-fill token and email from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('token')) {
            document.getElementById('token').value = urlParams.get('token');
        }
        if (urlParams.get('email')) {
            document.getElementById('email').value = urlParams.get('email');
        }
    </script>
</body>
</html>
