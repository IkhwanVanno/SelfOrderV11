@extends('layouts.app')

@section('content') 
<section class="w-full py-4">
  <div class="max-w-6xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">

      <!-- Header -->
      <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800">User Management</h2>
        <button onclick="openModal('add')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          <span>Add User</span>
        </button> 
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full min-w-[640px]">
          <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Username</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Role</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($users as $user)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-900">{{ $user->username }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full">{{ ucfirst($user->role) }}</span>
                </td>
                <td class="px-6 py-4 flex flex-col sm:flex-row gap-2">
                  <button onclick="openModal('edit', {{ $user->id }}, '{{ $user->username }}', '{{ $user->role }}')" class="px-3 py-1 text-sm text-blue-600 border border-blue-300 bg-blue-50 rounded hover:bg-blue-100">Edit</button>
                  <button onclick="deleteUser({{ $user->id }})" class="px-3 py-1 text-sm text-red-600 border border-red-300 bg-red-50 rounded hover:bg-red-100">Delete</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full sm:max-w-md transform transition-all duration-300 scale-95" id="modalContent">
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 rounded-t-lg flex justify-between items-center">
        <h3 id="modalTitle" class="text-white text-lg font-semibold">Add User</h3>
        <button onclick="closeModal()" class="text-white hover:text-gray-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-4">
        <form id="userForm">
          @csrf
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Username *</label>
            <input type="text" id="username" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Password *</label>
            <input type="password" id="password" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-3" id="confirmPasswordDiv">
            <label class="block text-sm font-medium mb-1">Confirm Password *</label>
            <input type="password" id="confirmPassword" class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Role *</label>
            <select id="role" required class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
              <option value="">Select role</option>
              <option value="admin">Admin</option>
              <option value="user">User</option>
            </select>
          </div>
          <div class="flex space-x-2">
            <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border text-gray-700 rounded hover:bg-gray-100">Cancel</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              <span id="submitButtonText">Add User</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>
    let currentMode = "add";
    let editingUserId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function openModal(mode, id = null, username = "", role = "") {
      currentMode = mode;
      editingUserId = id;

      document.getElementById("userForm").reset();
      document.getElementById("password").type = "password";

      const title = document.getElementById("modalTitle");
      const buttonText = document.getElementById("submitButtonText");
      const confirmPasswordDiv = document.getElementById("confirmPasswordDiv");

      if (mode === "edit") {
        title.textContent = "Edit User";
        buttonText.textContent = "Update User";
        confirmPasswordDiv.style.display = "none";
        document.getElementById("username").value = username;
        document.getElementById("role").value = role;
      } else {
        title.textContent = "Add User";
        buttonText.textContent = "Add User";
        confirmPasswordDiv.style.display = "block";
      }

      document.getElementById("modalOverlay").classList.remove("hidden");
      document.getElementById("modalOverlay").classList.add("flex");

      setTimeout(() => {
        document.getElementById("modalContent").classList.remove("scale-95");
        document.getElementById("modalContent").classList.add("scale-100");
      }, 10);
    }

    function closeModal() {
      const modal = document.getElementById("modalOverlay");
      const content = document.getElementById("modalContent");

      content.classList.remove("scale-100");
      content.classList.add("scale-95");

      setTimeout(() => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
      }, 300);
    }

    document.getElementById("userForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirmPassword").value;
      const role = document.getElementById("role").value;

      if (currentMode === "add") {
        if (password.length < 6) return alert("Password minimal 6 karakter");
        if (password !== confirmPassword) return alert("Konfirmasi password tidak cocok");
      }

      const data = { username, password, role };
      const url = currentMode === "add" ? "/user" : `/user/${editingUserId}`;
      const method = currentMode === "add" ? "POST" : "PUT";

      fetch(url, {
        method: method,
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(response => {
        alert(response.message);
        location.reload();
      })
      .catch(err => {
        console.error(err);
        alert("Terjadi kesalahan");
      });

      closeModal();
    });

    function deleteUser(id) {
      if (!confirm("Yakin ingin menghapus user ini?")) return;

      fetch(`/user/${id}`, {
        method: "DELETE",
        headers: {
          "X-CSRF-TOKEN": csrfToken
        }
      })
      .then(res => res.json())
      .then(response => {
        alert(response.message);
        location.reload();
      })
      .catch(err => {
        console.error(err);
        alert("Gagal menghapus user");
      });
    }

    document.getElementById("modalOverlay").addEventListener("click", function(e) {
      if (e.target === this) closeModal();
    });

    document.addEventListener("keydown", function(e) {
      if (e.key === "Escape") closeModal();
    });
  </script>
</section>
@endsection
