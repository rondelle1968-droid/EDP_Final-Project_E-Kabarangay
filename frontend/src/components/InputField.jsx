import React from 'react';

const InputField = ({ label, type, name, placeholder, required }) => {
  return (
    <div style={{ marginBottom: '1.25rem', display: 'flex', flexDirection: 'column' }}>
      <label style={{ 
        marginBottom: '0.5rem', 
        fontSize: '0.85rem', 
        fontWeight: '600', 
        color: '#4a5568',
        textAlign: 'left'
      }}>
        {label}
      </label>
      <input 
        type={type} 
        name={name} 
        placeholder={placeholder} 
        required={required}
        style={{
          padding: '12px 16px',
          borderRadius: '25px', // Pill-shape style kung gusto mo ng minimalist
          border: '1px solid #cbd5e1',
          fontSize: '1rem',
          outline: 'none',
          transition: 'border-color 0.2s',
          backgroundColor: '#f8fafc'
        }}
        onFocus={(e) => e.target.style.borderColor = '#3b82f6'}
        onBlur={(e) => e.target.style.borderColor = '#cbd5e1'}
      />
    </div>
  );
};

export default InputField;