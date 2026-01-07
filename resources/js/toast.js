// Usage:
// toastSuccess("Success message");
// toastError("Error message");
// toastInfo("Info message");
// toastWarning("Warning message");

// Success Toast
function toastSuccess(message) {
  Toastify({
    text: message,
    duration: 3000,
    gravity: "top",
    position: "right",
    backgroundColor: "#4CAF50", // Solid green for success
    stopOnFocus: true,
    borderRadius: "8px", // Added border-radius
  }).showToast();
}

// Error Toast
function toastError(message) {
  Toastify({
    text: message,
    duration: 3000,
    gravity: "top",
    position: "right",
    backgroundColor: "#D32F2F", // Solid red for error
    stopOnFocus: true,
    borderRadius: "8px", // Added border-radius
  }).showToast();
}

// Info Toast
function toastInfo(message) {
  Toastify({
    text: message,
    duration: 3000,
    gravity: "bottom",
    position: "right",
    backgroundColor: "#2196F3", // Solid blue for info
    stopOnFocus: true,
    borderRadius: "8px", // Added border-radius
  }).showToast();
}

// Warning Toast
function toastWarning(message) {
  Toastify({
    text: message,
    duration: 3000,
    gravity: "bottom",
    position: "right",
    backgroundColor: "#FFA000", // Solid amber for warning
    stopOnFocus: true,
    borderRadius: "8px", // Added border-radius
  }).showToast();
}
