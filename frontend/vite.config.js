import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  // I-load ang opisyal na react plugin
  plugins: [react()],
  build: {
    // I-build ang files direkta sa labas (sa root ng iyong PHP project)
    outDir: '../dist',
    emptyOutDir: true,
    assetsDir: '',
    rollupOptions: {
      output: {
        // Tanggalin ang hashes sa file names para permanenteng 'react-login.js' at 'react-login.css'
        entryFileNames: 'react-login.js',
        assetFileNames: 'react-login.[ext]'
      }
    }
  }
});