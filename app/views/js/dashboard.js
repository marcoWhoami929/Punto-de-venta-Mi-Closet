//--------------------------------------------------
const ctx = document.getElementById("myChart");
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
const ctx2 = document.getElementById("myChart2");
Chart.defaults.datasets.line.showLine = true;
const data = {
  labels: ["January", "February", "March", "April", "May", "June", "July"],
  datasets: [
    {
      label: "Looping tension",
      data: [65, 59, 80, 81, 26, 55, 40],
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
const ctx3 = document.getElementById("myChart3");
new Chart(ctx3, {
  type: "doughnut",
  data: {
    labels: ["Red", "Blue", "Yellow"],
    datasets: [
      {
        label: "My First Dataset",
        data: [300, 50, 100],
        backgroundColor: [
          "rgb(255, 99, 132)",
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
