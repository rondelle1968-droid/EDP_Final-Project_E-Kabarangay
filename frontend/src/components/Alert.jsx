import React from 'react';

const Alert = ({ message }) => {
  if (!message) return null;
  return (
    <div style={{
      padding: '12px 16px',
      backgroundColor: '#fde8e8',
      borderLeft: '4px solid #e74c3c',
      color: '#e74c3c',
      borderRadius: '6px',
      marginBottom: '1.5rem',
      fontWeight: '600',
      fontSize: '0.9rem',
      display: 'flex',
      alignItems: 'center',
      animation: 'fadeIn 0.3s ease'
    }}>
      <span style={{ marginRight: '8px' }}>⚠️</span>
      {message}
    </div>
  );
};

export default Alert;