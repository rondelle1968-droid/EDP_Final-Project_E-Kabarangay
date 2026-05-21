import React from 'react';
import Alert from './components/Alert';
import InputField from './components/InputField';
import LoginButton from './components/LoginButton';

function App() {
  // Kukunin natin ang error string mula sa PHP na ibabato sa window object
  const phpError = window.phpError || '';

  return (
    <div className="login-card" style={{ background: '#fff', padding: '2.5rem', borderRadius: '16px', boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1)' }}>
      <h2 style={{ fontSize: '1.75rem', fontWeight: '800', marginBottom: '0.5rem', color: '#1e293b' }}>Welcome Back</h2>
      <p style={{ color: '#64748b', marginBottom: '2rem', fontSize: '0.95rem' }}>Please enter your details to sign in.</p>
      
      {/* React Alert Component */}
      <Alert message={phpError} />

      {/* Ang standard HTML Form submission papuntang PHP backend pa rin */}
      <form method="POST" action="">
        <InputField 
          label="Username" 
          type="text" 
          name="username" 
          placeholder="ex. JuanDelaCruz01" 
          required 
        />
        <InputField 
          label="Password" 
          type="password" 
          name="password" 
          placeholder="••••••••" 
          required 
        />
        
        <div className="form-footer" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.5rem', fontSize: '0.85rem' }}>
          <label className="checkbox-group" style={{ display: 'flex', alignItems: 'center', color: '#64748b', cursor: 'pointer' }}>
            <input type="checkbox" name="remember" style={{ marginRight: '6px' }} /> Remember me
          </label>
          <a href="#" className="forgot-link" style={{ color: '#3b82f6', textDecoration: 'none', fontWeight: '600' }}>Forgot Password?</a>
        </div>

        {/* React Button Component */}
        <LoginButton />
        
        <p className="register-text" style={{ marginTop: '1.5rem', textAlign: 'center', fontSize: '0.9rem', color: '#64748b' }}>
          Don't have an account? <a href="register_details.php" style={{ color: '#3b82f6', textDecoration: 'none', fontWeight: '600' }}>Register</a>
        </p>
      </form>
    </div>
  );
}

export default App;