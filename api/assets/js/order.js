
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const serviceSelect = document.getElementById('service');
    const quantityInput = document.getElementById('quantity');
    const priceDisplay = document.getElementById('priceDisplay');
    const priceInput = document.getElementById('price');
    const minQuantityDisplay = document.getElementById('minQuantity');
    const orderBtn = document.getElementById('orderBtn');
    
    // Store services data
    let servicesData = {};
    
    // Handle category change
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        if (!categoryId) {
            serviceSelect.innerHTML = '<option value="">Select Category First</option>';
            serviceSelect.disabled = true;
            return;
        }
        
        // Fetch services for the selected category
        fetch(`../api/get_services.php?category_id=${categoryId}`)
            .then(response => response.json())
            .then(data => {
                servicesData = {};
                
                // Create options for service select
                let options = '<option value="">Select Service</option>';
                data.forEach(service => {
                    options += `<option value="${service.id}">${service.name} - $${parseFloat(service.price_per_1000).toFixed(2)} per 1000</option>`;
                    
                    // Store service data for calculations
                    servicesData[service.id] = {
                        price_per_1000: parseFloat(service.price_per_1000),
                        min_quantity: parseInt(service.min_quantity),
                        max_quantity: parseInt(service.max_quantity)
                    };
                });
                
                serviceSelect.innerHTML = options;
                serviceSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching services:', error);
                
                // Simulate API response for demo purposes
                simulateServicesData(categoryId);
            });
    });
    
    // Function to simulate API response (for demo purposes)
    function simulateServicesData(categoryId) {
        servicesData = {};
        let services = [];
        
        if (categoryId == '1') { // YouTube
            services = [
                { id: 1, name: 'YouTube Views', price_per_1000: 5.00, min_quantity: 100, max_quantity: 100000 },
                { id: 2, name: 'YouTube Subscribers', price_per_1000: 20.00, min_quantity: 100, max_quantity: 10000 },
                { id: 3, name: 'YouTube Likes', price_per_1000: 10.00, min_quantity: 100, max_quantity: 50000 }
            ];
        } else if (categoryId == '2') { // Facebook
            services = [
                { id: 4, name: 'Facebook Views', price_per_1000: 3.00, min_quantity: 100, max_quantity: 100000 },
                { id: 5, name: 'Facebook Likes', price_per_1000: 8.00, min_quantity: 100, max_quantity: 50000 },
                { id: 6, name: 'Facebook Followers', price_per_1000: 15.00, min_quantity: 100, max_quantity: 10000 }
            ];
        } else if (categoryId == '3') { // TikTok
            services = [
                { id: 7, name: 'TikTok Views', price_per_1000: 4.00, min_quantity: 100, max_quantity: 100000 },
                { id: 8, name: 'TikTok Likes', price_per_1000: 7.00, min_quantity: 100, max_quantity: 50000 },
                { id: 9, name: 'TikTok Followers', price_per_1000: 18.00, min_quantity: 100, max_quantity: 10000 }
            ];
        } else if (categoryId == '4') { // Instagram
            services = [
                { id: 10, name: 'Instagram Followers', price_per_1000: 12.00, min_quantity: 100, max_quantity: 50000 },
                { id: 11, name: 'Instagram Likes', price_per_1000: 6.00, min_quantity: 100, max_quantity: 100000 },
                { id: 12, name: 'Instagram Views', price_per_1000: 3.50, min_quantity: 100, max_quantity: 500000 }
            ];
        }
        
        // Create options for service select
        let options = '<option value="">Select Service</option>';
        services.forEach(service => {
            options += `<option value="${service.id}">${service.name} - $${parseFloat(service.price_per_1000).toFixed(2)} per 1000</option>`;
            
            // Store service data for calculations
            servicesData[service.id] = {
                price_per_1000: parseFloat(service.price_per_1000),
                min_quantity: parseInt(service.min_quantity),
                max_quantity: parseInt(service.max_quantity)
            };
        });
        
        serviceSelect.innerHTML = options;
        serviceSelect.disabled = false;
    }
    
    // Handle service change
    serviceSelect.addEventListener('change', function() {
        const serviceId = this.value;
        
        if (!serviceId || !servicesData[serviceId]) {
            resetOrderForm();
            return;
        }
        
        const service = servicesData[serviceId];
        
        // Set min/max quantity
        quantityInput.min = service.min_quantity;
        quantityInput.max = service.max_quantity;
        quantityInput.value = service.min_quantity;
        minQuantityDisplay.textContent = service.min_quantity;
        
        // Calculate and display price
        calculatePrice();
        
        // Enable order button
        orderBtn.disabled = false;
    });
    
    // Handle quantity change
    quantityInput.addEventListener('input', calculatePrice);
    
    // Calculate price based on selected service and quantity
    function calculatePrice() {
        const serviceId = serviceSelect.value;
        const quantity = parseInt(quantityInput.value) || 0;
        
        if (!serviceId || !servicesData[serviceId]) {
            priceDisplay.textContent = '0.00';
            priceInput.value = 0;
            return;
        }
        
        const service = servicesData[serviceId];
        const price = (service.price_per_1000 / 1000) * quantity;
        
        priceDisplay.textContent = formatMoney(price);
        priceInput.value = price;
    }
    
    // Reset order form
    function resetOrderForm() {
        quantityInput.min = 100;
        quantityInput.max = 10000;
        quantityInput.value = '';
        minQuantityDisplay.textContent = '100';
        priceDisplay.textContent = '0.00';
        priceInput.value = 0;
        orderBtn.disabled = true;
    }
});