function navigate(page) {
  // Change URL
  window.location.href = `?page=${page}`;
}

document.addEventListener('DOMContentLoaded', () => {
  // Get all buttons
  const buttons = document.querySelectorAll('.tabs button');
  const currentPage = new URLSearchParams(window.location.search).get('page') || 'doctors';

  // Set active button based on current page
  buttons.forEach((button) => {
    if (button.textContent.toLowerCase() === currentPage) {
      button.classList.add('active');
    } else {
      button.classList.remove('active');
    }
  });
});


// Dummy script to handle button clicks and simulate interaction
document.addEventListener("DOMContentLoaded", () => {
  console.log("s UI loaded!");
});

const appointments = {
  "2024-06-03": [
    { time: "09:00 - 10:30 AM", doctor: "Dr. Samantha", status: "confirmed" },
    { time: "10:30 - 12:00 PM", doctor: "Dr. Samantha", status: "cancelled" }
  ],
  "2024-06-16": [
    { time: "09:00 - 10:30 AM", doctor: "Dr. Samantha", status: "confirmed" }
  ],
  "2024-06-27": [
    { time: "09:00 - 10:30 AM", doctor: "Dr. Samantha", status: "cancelled" }
  ]
};

const calendarBody = document.getElementById("calendar-body");
const appointmentsList = document.getElementById("appointments");
const monthYear = document.getElementById("month-year");
let currentMonth = 5; // May
let currentYear = 2024;

function populateCalendar() {
  calendarBody.innerHTML = "";
  const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

  let date = new Date(currentYear, currentMonth - 1, 1);
  let firstDay = date.getDay();

  let row = document.createElement("tr");

  for (let i = 0; i < firstDay; i++) {
    row.appendChild(document.createElement("td"));
  }

  for (let day = 1; day <= daysInMonth; day++) {
    if (firstDay % 7 === 0 && day !== 1) {
      calendarBody.appendChild(row);
      row = document.createElement("tr");
    }

    const cell = document.createElement("td");
    cell.textContent = day;

    const fullDate = `${currentYear}-${String(currentMonth).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

    if (appointments[fullDate]) {
      cell.classList.add("has-appointments");
      cell.addEventListener("click", () => showAppointments(fullDate));
    }

    row.appendChild(cell);
    firstDay++;
  }
  calendarBody.appendChild(row);

  monthYear.textContent = `${date.toLocaleString("default", { month: "long" })} ${currentYear}`;
}

function showAppointments(date) {
  appointmentsList.innerHTML = "";
  const dailyAppointments = appointments[date] || [];
  dailyAppointments.forEach(appointment => {
    const item = document.createElement("li");
    item.classList.add("info-card");
    item.innerHTML = `
        <div class="event-details">
            <span class="event-label">Doctor:</span>
            <span class="event-value">${appointment.doctor}</span>
        </div>
        <div class="amount-details">
            <span class="amount-label">Time</span>
            <span class="amount-value">${appointment.time}</span>
        </div>
        <div>
          <span class="status-icon ${appointment.status === "confirmed" ? "green" : "red"}">
            ${appointment.status === "confirmed" ? "✔️" : "❌"}
          </span>
        </div>
      `;
    appointmentsList.appendChild(item);
  });
}

document.getElementById("prev-month").addEventListener("click", () => {
  currentMonth--;
  if (currentMonth === 0) {
    currentMonth = 12;
    currentYear--;
  }
  populateCalendar();
});

document.getElementById("next-month").addEventListener("click", () => {
  currentMonth++;
  if (currentMonth === 13) {
    currentMonth = 1;
    currentYear++;
  }
  populateCalendar();
});

populateCalendar();


