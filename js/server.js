require('dotenv').config();
const express = require('express');
const axios = require('axios');
const cors = require('cors');
const { exec } = require('child_process');

const app = express();
const PORT = process.env.PORT || 3000;

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
    const pingProcess = exec(`ping -c 3 ${ip}`, { timeout: 5000 }, (error) => {
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
const server = app.listen(PORT, () => {
  console.log(`Servidor de control escuchando en https://hackforfun.io:${PORT}`);
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