const API_GESTION = '/api/gestion.php';
const ESTADO_LABEL = {
  'Disponible': 'Disponible',
  'Req. Asistencia': 'Requiere asistencia',
  'Req. Asistencia Inmediata': 'Requiere asistencia inmediata'
};
const map = L.map('map', {zoomControl:true}).setView([-34.9075, -56.1850], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'&copy; OpenStreetMap', maxZoom:19}).addTo(map);
const markers = new Map();
const containers = new Map();
const track = document.getElementById('panelTrack');
const dots = document.querySelectorAll('.dot');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const elementEmpty = document.getElementById('elementEmpty');
const elementFilled = document.getElementById('elementFilled');
const elType = document.getElementById('elType');
const elSubtype = document.getElementById('elSubtype');
const elInfo = document.getElementById('elInfo');
const elIcon = document.getElementById('elIcon');
let selectedContainer = null;
let currentView = 0;

async function apiGetContainer(id) {
  const response = await fetch(`${API_GESTION}?resource=contenedor&id=${encodeURIComponent(id)}`, {credentials:'same-origin'});
  const data = await response.json();
  if (!response.ok || data.status !== 'success' || !data.data) throw new Error(data.message || 'Contenedor no encontrado');
  return data.data;
}

function pinIcon(state) {
  const color = state === 'Disponible' ? 'verde' : state === 'Req. Asistencia' ? 'amarillo' : 'rojo';
  return L.divIcon({className:'', html:`<div class="container-pin ${color}"></div>`, iconSize:[22,22], iconAnchor:[11,22]});
}

function setView(index) {
  currentView = Math.max(0, Math.min(1, index));
  track.style.transform = `translateX(-${currentView * 50}%)`;
  dots.forEach((dot, i) => dot.classList.toggle('active', i === currentView));
  prevBtn.disabled = currentView === 0;
  nextBtn.disabled = currentView === 1;
}
prevBtn.addEventListener('click', () => setView(currentView - 1));
nextBtn.addEventListener('click', () => setView(currentView + 1));

function showElement(container) {
  selectedContainer = container;
  elementEmpty.hidden = true;
  elementFilled.hidden = false;
  elType.textContent = `Contenedor ${container.N_Serie || `#${container.ID_Elemento}`}`;
  elSubtype.textContent = `${container.Tipo_Cont || 'Tipo no informado'} · ${container.Tipo_Res || 'Residuo no informado'}`;
  elInfo.textContent = ESTADO_LABEL[container.Estado] || container.Estado || 'Estado no informado';
  elIcon.className = 'element-icon';
  elIcon.style.color = container.Estado === 'Disponible' ? '#4C9A6A' : container.Estado === 'Req. Asistencia' ? '#E3AE3F' : '#D1583F';
  setView(1);
}

function addContainer(container) {
  const id = Number(container.ID_Elemento);
  const lat = Number(container.Latitud);
  const lng = Number(container.Longitud);
  if (!Number.isFinite(id) || !Number.isFinite(lat) || !Number.isFinite(lng)) return false;
  containers.set(id, container);
  if (markers.has(id)) map.removeLayer(markers.get(id));
  const marker = L.marker([lat, lng], {icon:pinIcon(container.Estado)}).addTo(map);
  marker.bindPopup(`<strong>${String(container.N_Serie || `Contenedor ${id}`).replace(/[&<>"']/g, '')}</strong><br>${String(container.Tipo_Res || '').replace(/[&<>"']/g, '')}<br>${String(container.Estado || '').replace(/[&<>"']/g, '')}`);
  marker.on('click', () => showElement(container));
  markers.set(id, marker);
  const li = document.createElement('li');
  li.className = 'inbox-item';
  const title = document.createElement('span');
  title.className = 'inbox-item-subject';
  title.textContent = `${container.N_Serie || `Contenedor ${id}`} · ${container.Tipo_Res || 'Residuo no informado'}`;
  const detail = document.createElement('p');
  detail.className = 'inbox-item-preview';
  detail.textContent = ESTADO_LABEL[container.Estado] || container.Estado || 'Estado no informado';
  li.append(title, detail);
  li.addEventListener('click', () => {
    showElement(container);
    map.setView([lat, lng], 16);
    marker.openPopup();
  });
  document.getElementById('inboxList').appendChild(li);
  return true;
}

async function loadContainer(id) {
  const status = document.getElementById('containerStatus');
  status.textContent = `Consultando contenedor ${id}…`;
  try {
    const container = await apiGetContainer(id);
    if (addContainer(container)) {
      status.textContent = `${containers.size} contenedor(es) cargado(s).`;
      map.setView([Number(container.Latitud), Number(container.Longitud)], 16);
      showElement(container);
    }
  } catch (error) {
    status.textContent = error.message;
  }
}

document.getElementById('loadContainerBtn').addEventListener('click', () => {
  const id = Number(document.getElementById('containerIdInput').value);
  if (!Number.isInteger(id) || id < 1) {
    document.getElementById('containerStatus').textContent = 'Ingresá un ID válido.';
    return;
  }
  loadContainer(id);
});
document.getElementById('containerIdInput').addEventListener('keydown', event => {
  if (event.key === 'Enter') document.getElementById('loadContainerBtn').click();
});
document.getElementById('reportBtn').addEventListener('click', () => {
  alert('La API actual no incluye un endpoint para crear incidencias.');
});

async function checkSession() {
  const response = await fetch('/api/auth.php?action=check_session', {credentials:'same-origin'});
  const data = await response.json();
  if (!response.ok || data.status !== 'success' || !data.logged_in) {
    window.location.href = '../login.html';
    return false;
  }
  return true;
}

async function initializeMap() {
  setView(0);
  try {
    if (!await checkSession()) return;
    document.getElementById('containerStatus').textContent = 'La API permite consultar contenedores por ID. Ingresá un ID para ubicarlo en el mapa.';
  } catch (error) {
    document.getElementById('containerStatus').textContent = 'No se pudo validar la sesión.';
    window.location.href = '../login.html';
  }
}
initializeMap();
