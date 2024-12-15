
//--------------------------------------------------
const ctx2 = document.getElementById("chart-ventas-mensuales");
Chart.defaults.datasets.line.showLine = true;

const data = {
  labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre","Diciembre"],
  datasets: [
    {
      label: "Ventas Mensuales",
      data: [0,0,0,0,0,0,0,0,0,0,0,localStorage.getItem("ventas-diciembre")],
      fill: false,
      borderColor: "rgb(75, 192, 192)",
    },
  ],
};
const chart = new Chart(ctx2, {
  type: "line",
  data: data,
  options: {
    animations: {
      tension: {
        duration: 1000,
        easing: "linear",
        from: 1,
        to: 0,
        loop: true,
      },
    },
    scales: {
      y: {
        min: 0,
        max: 100,
      },
    },
  },
});
//--------------------------------------------------
const ctx3 = document.getElementById("chart-ventas-notas");
var ventas = localStorage.getItem("ventas-totales")-localStorage.getItem("ventas-notas");
new Chart(ctx3, {
  type: "doughnut",
  data: {
    labels: ["Ventas", "Notas"],
    datasets: [
      {
        label: "",
        data: [ventas, localStorage.getItem("ventas-notas")],
        backgroundColor: [
     
          "rgb(54, 162, 235)",
          "rgb(255, 205, 86)",
        ],
        hoverOffset: 4,
      },
    ],
  },
});
//--------------------------------------------------
