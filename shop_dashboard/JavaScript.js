// ตั้งค่า API base URL
const API_BASE_URL = 'http://localhost/your-project/api';
let authToken = localStorage.getItem('auth_token');

// ฟังก์ชันสำหรับเรียก API
async function apiCall(endpoint, method = 'GET', data = null) {
    const headers = {
        'Content-Type': 'application/json',
    };
    
    if (authToken) {
        headers['Authorization'] = `Bearer ${authToken}`;
    }
    
    const config = {
        method,
        headers,
    };
    
    if (data) {
        config.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// ตัวอย่างการใช้งาน
async function loadProducts() {
    try {
        const result = await apiCall('/products/');
        if (result.success) {
            // อัปเดต UI ด้วยข้อมูลสินค้า
            displayProducts(result.data);
        }
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// เข้าสู่ระบบ
async function login(email, password) {
    try {
        const result = await apiCall('/auth/login', 'POST', { email, password });
        if (result.success) {
            authToken = result.token;
            localStorage.setItem('auth_token', authToken);
            localStorage.setItem('user_data', JSON.stringify(result.user));
            return result.user;
        }
        throw new Error(result.message);
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
}