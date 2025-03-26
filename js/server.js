require('dotenv').config();
const express = require('express');
const axios = require('axios');
const cors = require('cors');

const app = express();
const PORT = 3000;

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

// Mapeo de servidores
const SERVERS = {
  'behind-the-web': process.env.BEHIND_THE_WEB_SERVER_ID
};

// Middleware de logs
app.use((req, res, next) => {
  console.log(`${new Date().toISOString()} - ${req.method} ${req.url}`);
  next();
});

// Endpoint de estado
app.get('/server-status', async (req, res) => {
  try {
    const { machine } = req.query;
    const serverId = SERVERS[machine];
    
    if (!serverId) {
      return res.status(400).json({ 
        success: false, 
        error: 'Máquina no especificada o no existe' 
      });
    }

    const response = await cloudingApi.get(`/servers/${serverId}`);
    const server = response.data;

    res.json({
      success: true,
      status: server.powerState === 'Running' ? 'Running' : 'Stopped',
      ip: server.publicIp
    });
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    res.status(500).json({ 
      success: false, 
      error: error.response?.data?.message || error.message 
    });
  }
});

// Endpoint de acciones
app.post('/server-action', async (req, res) => {
  try {
    const { action, machine } = req.body;
    const serverId = SERVERS[machine];
    
    if (!serverId) {
      return res.status(400).json({ 
        success: false, 
        error: 'Máquina no especificada o no existe' 
      });
    }

    let endpoint;
    switch (action) {
      case 'start':
        endpoint = `/servers/${serverId}/start`;
        break;
      case 'stop':
        endpoint = `/servers/${serverId}/stop`;
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
  console.log(`Servidor de control escuchando en http://localhost:${PORT}`);
});

// Manejo de errores del servidor
server.on('error', (error) => {
  if (error.code === 'EADDRINUSE') {
    console.error(`ERROR: El puerto ${PORT} está en uso`);
    console.log('Ejecuta: sudo kill -9 $(sudo lsof -t -i:3000)');
  } else {
    console.error('Error del servidor:', error);
  }
  process.exit(1);
});