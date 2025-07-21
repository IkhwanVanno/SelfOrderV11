@extends('layouts.app')

@section('content')
    
<section class="w-full py-4">
  <div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <!-- Table Header -->
      <div class="bg-gray-50 px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <h2 class="text-xl font-semibold text-gray-800">Product Management</h2>
        <button onclick="openModal('add')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
          + Add Product
        </button>
      </div>
      
      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-[600px] w-full">
          <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Picture</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Name</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Price</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Category</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-300 rounded-md flex items-center justify-center overflow-hidden">
                  @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                  @else
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  @endif
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">RP {{ number_format($product->price, 0, ',', '.') }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($product->category) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100' }}">
                  {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm flex flex-wrap gap-2">
                <button onclick="openModal('edit', {{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->price }}', '{{ $product->category }}', {{ $product->is_active ? 'true' : 'false' }})" class="px-3 py-1 text-sm border border-blue-300 text-blue-600 rounded hover:bg-blue-50 transition-colors duration-200 w-[100%]">Edit</button>
                <button onclick="deleteProduct({{ $product->id }})" class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded hover:bg-red-50 transition-colors duration-200 w-[100%]">Delete</button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">No products found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Modal Overlay -->
  <div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all duration-300 scale-95 sm:scale-95" id="modalContent">
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-lg">
        <div class="flex items-center justify-between">
          <h3 id="modalTitle" class="text-lg font-semibold text-white">Add Product</h3>
          <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Modal Body -->
      <div class="p-6">
        <form id="productForm" enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="productId" name="product_id">
          <input type="hidden" id="methodField" name="_method" value="POST">
          
          <!-- Product Image Upload -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200">
              <div id="imagePreview" class="mb-4">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
              <p class="text-sm text-gray-600 mb-2">Click to upload or drag and drop</p>
              <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
              <input type="file" id="productImage" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
              <button type="button" onclick="document.getElementById('productImage').click()" class="mt-3 bg-blue-50 text-blue-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-100 transition-colors duration-200">
                Choose File
              </button>
            </div>
          </div>
          
          <!-- Product Name -->
          <div class="mb-4">
            <label for="productName" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
            <input type="text" id="productName" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" placeholder="Enter product name">
            <div id="nameError" class="text-red-500 text-sm mt-1 hidden"></div>
          </div>
          
          <!-- Product Price -->
          <div class="mb-4">
            <label for="productPrice" class="block text-sm font-medium text-gray-700 mb-2">Price (RP) *</label>
            <input type="number" id="productPrice" name="price" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" placeholder="0" min="0" step="0.01">
            <div id="priceError" class="text-red-500 text-sm mt-1 hidden"></div>
          </div>
          
          <!-- Product Category -->
          <div class="mb-4">
            <label for="productCategory" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
            <select id="productCategory" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
              <option value="">Select category</option>
              <option value="food">Food</option>
              <option value="drink">Drink</option>
              <option value="snack">Snack</option>
            </select>
            <div id="categoryError" class="text-red-500 text-sm mt-1 hidden"></div>
          </div>
          
          <!-- Product Status -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="flex items-center">
              <input type="checkbox" id="productStatus" name="is_active" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
              <label for="productStatus" class="ml-2 block text-sm text-gray-900">Active</label>
            </div>
          </div>
          
          <!-- Modal Footer -->
          <div class="flex space-x-3">
            <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors duration-200">
              Cancel
            </button>
            <button type="submit" id="submitButton" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
              <span id="submitButtonText">Add Product</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Success/Error Messages -->
  <div id="alertMessage" class="fixed top-4 right-4 z-60 hidden">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
      <span id="alertText"></span>
    </div>
  </div>
  
  <script>
    let currentMode = 'add';
    let currentProductId = null;
    
    // Get CSRF token
    function getCSRFToken() {
      // Try to get from meta tag first
      const metaTag = document.querySelector('meta[name="csrf-token"]');
      if (metaTag) {
        return metaTag.getAttribute('content');
      }
      
      // Fallback: get from Laravel's global variable if available
      if (typeof window.Laravel !== 'undefined' && window.Laravel.csrfToken) {
        return window.Laravel.csrfToken;
      }
      
      // Fallback: get from form token
      const tokenInput = document.querySelector('input[name="_token"]');
      if (tokenInput) {
        return tokenInput.value;
      }
      
      return '{{ csrf_token() }}';
    }
    
    const csrfToken = getCSRFToken();
    
    function openModal(mode, id = null, name = '', price = '', category = '', isActive = true) {
      currentMode = mode;
      currentProductId = id;
      
      const modal = document.getElementById('modalOverlay');
      const modalContent = document.getElementById('modalContent');
      const modalTitle = document.getElementById('modalTitle');
      const submitButtonText = document.getElementById('submitButtonText');
      const methodField = document.getElementById('methodField');
      const productId = document.getElementById('productId');
      
      // Reset form and clear errors
      document.getElementById('productForm').reset();
      clearValidationErrors();
      resetImagePreview();
      
      if (mode === 'edit') {
        modalTitle.textContent = 'Edit Product';
        submitButtonText.textContent = 'Update Product';
        methodField.value = 'PUT';
        productId.value = id;
        
        // Fill form with existing data
        document.getElementById('productName').value = name;
        document.getElementById('productPrice').value = price;
        document.getElementById('productCategory').value = category;
        document.getElementById('productStatus').checked = isActive;
      } else {
        modalTitle.textContent = 'Add Product';
        submitButtonText.textContent = 'Add Product';
        methodField.value = 'POST';
        productId.value = '';
        document.getElementById('productStatus').checked = true;
      }
      
      // Show modal with animation
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      
      setTimeout(() => {
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
      }, 10);
    }
    
    function closeModal() {
      const modal = document.getElementById('modalOverlay');
      const modalContent = document.getElementById('modalContent');
      
      modalContent.classList.remove('scale-100');
      modalContent.classList.add('scale-95');
      
      setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }, 300);
    }
    
    function previewImage(event) {
      const file = event.target.files[0];
      const previewContainer = document.getElementById('imagePreview');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" class="mx-auto h-24 w-24 object-cover rounded-lg">`;
        };
        reader.readAsDataURL(file);
      }
    }
    
    function resetImagePreview() {
      document.getElementById('imagePreview').innerHTML = `
        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
          <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      `;
    }
    
    function clearValidationErrors() {
      const errorElements = ['nameError', 'priceError', 'categoryError'];
      errorElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
          element.classList.add('hidden');
          element.textContent = '';
        }
      });
      
      // Reset input border colors
      const inputs = document.querySelectorAll('#productForm input, #productForm select');
      inputs.forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
      });
    }
    
    function showValidationErrors(errors) {
      clearValidationErrors();
      
      for (const [field, messages] of Object.entries(errors)) {
        const errorElement = document.getElementById(field + 'Error');
        const inputElement = document.getElementById('product' + field.charAt(0).toUpperCase() + field.slice(1));
        
        if (errorElement && inputElement) {
          errorElement.textContent = messages[0];
          errorElement.classList.remove('hidden');
          inputElement.classList.remove('border-gray-300');
          inputElement.classList.add('border-red-500');
        }
      }
    }
    
    function showAlert(message, type = 'success') {
      const alertDiv = document.getElementById('alertMessage');
      const alertText = document.getElementById('alertText');
      const alertContainer = alertDiv.querySelector('div');
      
      alertText.textContent = message;
      
      if (type === 'success') {
        alertContainer.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded';
      } else {
        alertContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
      }
      
      alertDiv.classList.remove('hidden');
      
      setTimeout(() => {
        alertDiv.classList.add('hidden');
      }, 5000);
    }
    
    // Form submission handler
    document.getElementById('productForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitButton = document.getElementById('submitButton');
      const submitButtonText = document.getElementById('submitButtonText');
      const originalText = submitButtonText.textContent;
      
      // Clear previous errors
      clearValidationErrors();
      
      // Determine URL based on mode
      let url;
      if (currentMode === 'add') {
        url = '{{ route("product.store") }}';
      } else {
        url = '{{ route("product.update", ":id") }}'.replace(':id', currentProductId);
      }
      
      // Show loading state
      submitButton.disabled = true;
      submitButtonText.textContent = 'Processing...';
      
      // Add CSRF token to form data
      formData.append('_token', csrfToken);
      
      // Debug log
      console.log('Submitting form:', {
        mode: currentMode,
        url: url,
        productId: currentProductId
      });
      
      fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async response => {
        const data = await response.json();
        
        if (!response.ok) {
          if (response.status === 422) {
            // Validation errors
            if (data.errors) {
              showValidationErrors(data.errors);
              return;
            }
          }
          throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }
        
        return data;
      })
      .then(data => {
        if (data && data.success) {
          showAlert(data.message, 'success');
          closeModal();
          // Reload page to show updated data
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          throw new Error(data.message || 'An error occurred');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert(error.message || 'An error occurred while saving the product', 'error');
      })
      .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButtonText.textContent = originalText;
      });
    });
    
    // Delete product function
    function deleteProduct(id) {
      if (confirm('Are you sure you want to delete this product?')) {
        fetch('{{ route("product.delete", ":id") }}'.replace(':id', id), {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(async response => {
          const data = await response.json();
          
          if (!response.ok) {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
          }
          
          return data;
        })
        .then(data => {
          if (data.success) {
            showAlert(data.message, 'success');
            // Reload page to show updated data
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else {
            throw new Error(data.message || 'An error occurred');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showAlert(error.message || 'An error occurred while deleting the product', 'error');
        });
      }
    }
    
    // Close modal when clicking outside
    document.getElementById('modalOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal();
      }
    });
  </script>
</section>
@endsection