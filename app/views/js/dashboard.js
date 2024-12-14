//--------------------------------------------------
const ctx = document.getElementById("myChart2");
new Chart(ctx, {
  type: "bar",
  data: {
    labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
    datasets: [
      {
        label: "# of Votes",
        data: [12, 19, 3, 5, 2, 3],
        borderWidth: 1,
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
});
//--------------------------------------------------
const ctx2 = document.getElementById("chart-ventas-mensuales");
Chart.defaults.datasets.line.showLine = true;
const data = {
  labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre","Diciembre"],
  datasets: [
    {
      label: "Ventas Mensuales",
      data: [
        $.ajax({
          url: "../app/ajax/posAjax.php",
          type: "POST",
          data: {
            
            modulo_pos: "ventasMensuales",
          },
          success: function (response) {
            var datos = JSON.parse(response);
            console.log(datos);
          }
        })
      ],
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
new Chart(ctx3, {
  type: "doughnut",
  data: {
    labels: ["Ventas", "Notas"],
    datasets: [
      {
        label: "",
        data: [300, 50,],
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
const ctx4 = document.getElementById("myChart4");
new Chart(ctx4, {
  type: "scatter",
  data: {
    labels: ["January", "February", "March", "April"],
    datasets: [
      {
        type: "bar",
        label: "Bar Dataset",
        data: [10, 20, 30, 40],
        borderColor: "rgb(255, 99, 132)",
        backgroundColor: "rgba(255, 99, 132, 0.2)",
      },
      {
        type: "line",
        label: "Line Dataset",
        data: [50, 50, 50, 50],
        fill: false,
        borderColor: "rgb(54, 162, 235)",
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
});
