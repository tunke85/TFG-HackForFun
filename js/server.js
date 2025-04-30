require('dotenv').config();
const express = require('express');
const axios = require('axios');
const cors = require('cors');
const { exec } = require('child_process');

const https = require('https');
const fs = require('fs');
const sslOptions = {
  key: fs.readFileSync('/etc/letsencrypt/live/www.hackforfun.io/privkey.pem'),
  cert: fs.readFileSync('/etc/letsencrypt/live/www.hackforfun.io/fullchain.pem'),
  minVersion: 'TLSv1.2', // Fuerza TLS 1.2
  secureOptions: require('constants').SSL_OP_NO_SSLv3 | require('constants').SSL_OP_NO_TLSv1 | require('constants').SSL_OP_NO_TLSv1_1,
  ciphers: 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384'
}; 

const app = express();
const PORT = process.env.PORT || 3000;

app.set('trust proxy', 1);

// Configuración
app.use(cors());
app.use(express.json());

// Configuración de Clouding
const CLOUDING_API_URL = 'https://api.clouding.io/v1';
const API_KEY = process.env.CLOUDING_API_KEY;

const cloudingApi = axios.create({
  baseURL: CLOUDING_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'X-API-KEY': API_KEY
  }
});

if (!process.env.CLOUDING_API_KEY) {
  console.error('ERROR: Falta CLOUDING_API_KEY en .env');
  process.exit(1);
}

// Función para verificar accesibilidad con ping (con timeout)
async function checkServerAccessibility(ip) {
  return new Promise((resolve) => {
    const pingProcess = exec(`ping -n 3 ${ip}`, { timeout: 5000 }, (error) => {
      resolve(!error);
    });
    
    // Timeout de seguridad
    setTimeout(() => {
      pingProcess.kill();
      resolve(false);
    }, 6000);
  });
}

// Middleware de logs
app.use((req, res, next) => {
  console.log(`${new Date().toISOString()} - ${req.method} ${req.url}`);
  next();
});

// Endpoint de estado modificado
app.get('/server-status', async (req, res) => {
  try {
    const { machine, serverId } = req.query; // Ahora recibimos serverId
    
    if (!serverId) {
      return res.status(400).json({ 
        success: false, 
        error: 'ID del servidor no especificado' 
      });
    }

    const response = await cloudingApi.get(`/servers/${serverId}`);
    const server = response.data;

    // Verificar accesibilidad si está running
    let isAccessible = false;
    if (server.status === 'active' && server.powerState === 'Running' && server.publicIp) {
      isAccessible = await checkServerAccessibility(server.publicIp);
    }

    res.json({
      success: true,
      status: server.status === 'archived' ? 'Archived' : server.powerState,
      ip: server.publicIp,
      isAccessible
    });
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    res.status(500).json({ 
      success: false, 
      error: error.response?.data?.message || error.message 
    });
  }
});

// Endpoint de acciones modificado
app.post('/server-action', async (req, res) => {
  try {
    const { action, machine, serverId } = req.body; // Recibimos serverId
    
    if (!serverId) {
      return res.status(400).json({ 
        success: false, 
        error: 'ID del servidor no especificado' 
      });
    }

    let endpoint;
    switch (action) {
      case 'start':
        endpoint = `/servers/${serverId}/unarchive`;
        break;
      case 'stop':
        endpoint = `/servers/${serverId}/archive`;
        break;
      case 'reboot':
        endpoint = `/servers/${serverId}/reboot`;
        break;
      default:
        return res.status(400).json({ 
          success: false, 
          error: 'Acción no válida' 
        });
    }

    await cloudingApi.post(endpoint);
    res.json({ success: true });
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    res.status(500).json({ 
      success: false, 
      error: error.response?.data?.message || error.message 
    });
  }
});

// Iniciar servidor
const server = https.createServer(sslOptions, app).listen(PORT, '0.0.0.0', () => {
  console.log(`Servidor HTTPS escuchando en http# Cabeceras CORS para preflight
    	Header always set Access-Control-Allow-Origin "https://hackforfun.io"
    	Header always set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    	Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
    	Header always set Access-Control-Allow-Credentials "true"

	RewriteEngine On
   	RewriteCond %{REQUEST_METHOD} OPTIONS
    	RewriteRule ^(.*)$ $1 [R=200,L]://hackforfun.io:${PORT}`);
});

server.on('error', (error) => {
  if (error.code === 'EADDRINUSE') {
    console.error(`ERROR: El puerto ${PORT} está en uso`);
    console.log('Ejecuta: sudo kill -9 $(sudo lsof -t -i:3000)');
  } else {
    console.error('Error del servidor:', error);
  }
  process.exit(1);
});
