import React from 'react';

const LoginButton = () => {
  return (
    <button 
      type="submit" 
      style={{
        width: '100%',
        padding: '12px',
        backgroundColor: '#3b82f6',
        color: '#ffffff',
        border: 'none',
        borderRadius: '25px', // Pill button aesthetic
        fontSize: '1rem',
        fontWeight: '600',
        cursor: 'pointer',
        transition: 'background-color 0.2s, transform 0.1s',
        boxShadow: '0 4px 6px -1px rgba(59, 130, 246, 0.3)'
      }}
      onMouseEnter={(e) => e.target.style.backgroundColor = '#2563eb'}
      onMouseLeave={(e) => e.target.style.backgroundColor = '#3b82f6'}
      onMouseDown={(e) => e.target.style.transform = 'scale(0.98)'}
      onMouseUp={(e) => e.target.style.transform = 'scale(1)'}
    >
      Login
    </button>
  );
};

export default LoginButton;