
const CONTAINERS = [
  {
    id: "c1",
    lat: -34.9058, lng: -56.1913,
    tipo: "Contenedor de residuos generales",
    subtipo: "Zona Centro · Ciudad Vieja",
    estado: "amarillo",
    info: "Nivel de llenado: 65%. Última recolección hace 2 días. Sin incidencias reportadas en la última semana."
  },
  {
    id: "c2",
    lat: -34.9155, lng: -56.1500,
    tipo: "Contenedor de reciclables",
    subtipo: "Zona Pocitos · Rambla",
    estado: "verde",
    info: "Nivel de llenado: 20%. Recolección al día. Sensor de tapa funcionando correctamente."
  },
  {
    id: "c3",
    lat: -34.9075, lng: -56.2078,
    tipo: "Contenedor de residuos generales",
    subtipo: "Zona Ciudad Vieja · Peatonal Sarandí",
    estado: "rojo",
    info: "Nivel de llenado: 95%. Recolección atrasada. Hay un reporte de incidencia activo para este contenedor."
  }
];

const REPORTS = [
  {
    asunto: "Contenedor desbordado",
    elemento: "Contenedor de residuos generales — Peatonal Sarandí",
    estado: "Pendiente",
    fecha: "22/07/2026",
    cuerpo: "El contenedor está completamente lleno desde hace tres días y los residuos se acumulan alrededor en la vereda. Se solicita recolección urgente antes del fin de semana."
  },
  {
    asunto: "Tapa rota",
    elemento: "Contenedor de reciclables — Rambla Pocitos",
    estado: "En revisión",
    fecha: "19/07/2026",
    cuerpo: "La tapa del contenedor está quebrada y no cierra bien, lo que permite que entre agua de lluvia y se mojen los reciclables."
  },
  {
    asunto: "Mal olor persistente",
    elemento: "Contenedor de residuos generales — Ciudad Vieja",
    estado: "Resuelto",
    fecha: "14/07/2026",
    cuerpo: "Vecinos reportan mal olor constante en la esquina. Se sugiere revisar la frecuencia de recolección en la zona."
  },
  {
    asunto: "Contenedor volcado por viento",
    elemento: "Contenedor de reciclables — Rambla Pocitos",
    estado: "Resuelto",
    fecha: "10/07/2026",
    cuerpo: "El contenedor se volcó durante la tormenta del fin de semana. Ya fue reubicado por el personal municipal."
  }
];

const ESTADO_LABEL = { verde: "Correcto", amarillo: "Atención", rojo: "Urgente" };


const map = L.map("map", { zoomControl: true }).setView([-34.9075, -56.1850], 13);

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: '&copy; OpenStreetMap',
  maxZoom: 19
}).addTo(map);

function pinIcon(estado) {
  return L.divIcon({
    className: "",
    html: `<div class="container-pin ${estado}"></div>`,
    iconSize: [22, 22],
    iconAnchor: [11, 22]
  });
}

CONTAINERS.forEach(c => {
  const marker = L.marker([c.lat, c.lng], { icon: pinIcon(c.estado) }).addTo(map);
  marker.bindPopup(`<b>${c.tipo}</b><br>${c.subtipo}`);
  marker.on("click", () => showElement(c));
});


const track = document.getElementById("panelTrack");
const dots = document.querySelectorAll(".dot");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
let currentView = 0;

function setView(index) {
  currentView = Math.max(0, Math.min(1, index));
  track.style.transform = `translateX(-${currentView * 50}%)`;
  dots.forEach((d, i) => d.classList.toggle("active", i === currentView));
  prevBtn.disabled = currentView === 0;
  nextBtn.disabled = currentView === 1;
}
prevBtn.addEventListener("click", () => setView(currentView - 1));
nextBtn.addEventListener("click", () => setView(currentView + 1));
setView(0);


const elementEmpty = document.getElementById("elementEmpty");
const elementFilled = document.getElementById("elementFilled");
const elType = document.getElementById("elType");
const elSubtype = document.getElementById("elSubtype");
const elInfo = document.getElementById("elInfo");
const elIcon = document.getElementById("elIcon");
const reportBtn = document.getElementById("reportBtn");

let selectedContainer = null;

function showElement(container) {
  selectedContainer = container;
  elementEmpty.hidden = true;
  elementFilled.hidden = false;
  elType.textContent = container.tipo;
  elSubtype.textContent = container.subtipo;
  elInfo.textContent = `${ESTADO_LABEL[container.estado]} — ${container.info}`;
  elIcon.className = "element-icon";
  elIcon.style.color = { verde: "#4C9A6A", amarillo: "#E3AE3F", rojo: "#D1583F" }[container.estado];
  setView(1);
}


const inboxList = document.getElementById("inboxList");

REPORTS.forEach((r, i) => {
  const li = document.createElement("li");
  li.className = "inbox-item";
  const chip = r.estado === "Resuelto" ? "verde" : (r.estado === "En revisión" ? "amarillo" : "rojo");
  li.innerHTML = `
    <div class="inbox-item-top">
      <span class="status-chip ${chip}"></span>
      <span class="inbox-item-subject">${r.asunto}</span>
    </div>
    <p class="inbox-item-preview">${r.cuerpo}</p>
  `;
  li.addEventListener("click", () => openInforme(r));
  inboxList.appendChild(li);
});


function openModal(id) { document.getElementById(id).classList.add("open"); }
function closeModal(id) { document.getElementById(id).classList.remove("open"); }

document.querySelectorAll("[data-close]").forEach(btn => {
  btn.addEventListener("click", () => closeModal(btn.dataset.close));
});
document.querySelectorAll(".modal-overlay").forEach(overlay => {
  overlay.addEventListener("click", e => {
    if (e.target === overlay) closeModal(overlay.id);
  });
});

function openInforme(r) {
  document.getElementById("informeAsunto").textContent = r.asunto;
  document.getElementById("informeElemento").textContent = `Elemento afectado: ${r.elemento}`;
  document.getElementById("informeEstado").textContent = `Estado: ${r.estado}`;
  document.getElementById("informeFecha").textContent = r.fecha;
  document.getElementById("informeCuerpo").textContent = r.cuerpo;
  openModal("informeModal");
}

const reportForm = document.getElementById("reportForm");
const reportSuccess = document.getElementById("reportSuccess");
const evidenceBtn = document.getElementById("evidenceBtn");
const evidenceInput = document.getElementById("evidenceInput");
const evidenceName = document.getElementById("evidenceName");

reportBtn.addEventListener("click", () => {
  if (!selectedContainer) return;
  const today = new Date().toLocaleDateString("es-UY");
  document.getElementById("reportAsunto").textContent = selectedContainer.tipo;
  document.getElementById("reportElemento").textContent = `Elemento afectado: ${selectedContainer.subtipo}`;
  document.getElementById("reportFecha").textContent = today;
  reportForm.hidden = false;
  reportSuccess.hidden = true;
  reportForm.reset();
  evidenceName.textContent = "";
  openModal("reportModal");
});

evidenceBtn.addEventListener("click", () => evidenceInput.click());
evidenceInput.addEventListener("change", () => {
  evidenceName.textContent = evidenceInput.files[0]?.name || "";
});

reportForm.addEventListener("submit", e => {
  e.preventDefault();
  reportForm.hidden = true;
  reportSuccess.hidden = false;
});
