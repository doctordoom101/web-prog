document.addEventListener("DOMContentLoaded", () => {
  // Elemen-elemen form dan tab
  const form = document.getElementById("ticketForm")
  const formTab = document.getElementById("formTab")
  const dataTab = document.getElementById("dataTab")
  const formContent = document.getElementById("formContent")
  const dataContent = document.getElementById("dataContent")
  const successAlert = document.getElementById("successAlert")
  const successMessage = document.getElementById("successMessage")
  const penumpangTableBody = document.getElementById("penumpangTableBody")
  const downloadButton = document.getElementById("downloadButton")
  const submitButton = document.getElementById("submitButton")
  const formTitle = document.getElementById("formTitle")

  // Set tanggal minimum untuk input tanggal (hari ini)
  const today = new Date().toISOString().split("T")[0]
  document.getElementById("tanggalBerangkat").min = today

  // Array untuk menyimpan data penumpang
  let penumpangList = []
  let editingId = null

  // Fungsi untuk validasi form
  function validateForm() {
    let isValid = true

    // Reset semua error
    const errorElements = document.querySelectorAll('[id$="Error"]')
    errorElements.forEach((el) => {
      el.classList.add("hidden")
      el.textContent = ""
    })

    // Reset border error
    const inputElements = form.querySelectorAll("input, select")
    inputElements.forEach((el) => {
      el.classList.remove("error")
    })

    // Validasi Nama
    const nama = document.getElementById("nama").value.trim()
    if (!nama) {
      showError("nama", "Nama tidak boleh kosong")
      isValid = false
    }

    // Validasi Email
    const email = document.getElementById("email").value.trim()
    if (!email.includes("@") || !email.includes(".")) {
      showError("email", 'Email harus valid (mengandung "@" dan ".")')
      isValid = false
    }

    // Validasi Nomor HP
    const nomorHP = document.getElementById("nomorHP").value.trim()
    if (!/^\d+$/.test(nomorHP)) {
      showError("nomorHP", "Nomor HP harus angka saja")
      isValid = false
    } else if (nomorHP.length < 10) {
      showError("nomorHP", "Nomor HP minimal 10 digit")
      isValid = false
    }

    // Validasi Jenis Kelamin
    const jenisKelamin = document.querySelector('input[name="jenisKelamin"]:checked')
    if (!jenisKelamin) {
      showError("jenisKelamin", "Jenis kelamin harus dipilih")
      isValid = false
    }

    // Validasi Kereta
    const kereta = document.getElementById("kereta").value
    if (!kereta) {
      showError("kereta", "Kereta harus dipilih")
      isValid = false
    }

    // Validasi Stasiun Asal
    const stasiunAsal = document.getElementById("stasiunAsal").value
    if (!stasiunAsal) {
      showError("stasiunAsal", "Stasiun asal harus dipilih")
      isValid = false
    }

    // Validasi Stasiun Tujuan
    const stasiunTujuan = document.getElementById("stasiunTujuan").value
    if (!stasiunTujuan) {
      showError("stasiunTujuan", "Stasiun tujuan harus dipilih")
      isValid = false
    }

    // Validasi stasiun asal dan tujuan tidak boleh sama
    if (stasiunAsal && stasiunAsal === stasiunTujuan) {
      showError("stasiunTujuan", "Stasiun tujuan tidak boleh sama dengan stasiun asal")
      isValid = false
    }

    // Validasi Tanggal Berangkat
    const tanggalBerangkat = document.getElementById("tanggalBerangkat").value
    if (!tanggalBerangkat) {
      showError("tanggalBerangkat", "Tanggal keberangkatan harus diisi")
      isValid = false
    }

    // Validasi Kelas
    const kelas = document.getElementById("kelas").value
    if (!kelas) {
      showError("kelas", "Kelas harus dipilih")
      isValid = false
    }

    return isValid
  }

  // Fungsi untuk menampilkan error
  function showError(fieldName, message) {
    const errorElement = document.getElementById(`${fieldName}Error`)
    const inputElement = document.getElementById(fieldName)

    if (errorElement) {
      errorElement.textContent = message
      errorElement.classList.remove("hidden")
    }

    if (inputElement) {
      inputElement.classList.add("error")
    }
  }

  // Fungsi untuk menampilkan pesan sukses
  function showSuccessMessage(message) {
    successMessage.textContent = message
    successAlert.classList.remove("hidden")
    successAlert.classList.add("fade-in")

    // Hilangkan pesan setelah 3 detik
    setTimeout(() => {
      successAlert.classList.add("fade-out")
      setTimeout(() => {
        successAlert.classList.add("hidden")
        successAlert.classList.remove("fade-out")
      }, 300)
    }, 3000)
  }

  // Fungsi untuk mendapatkan data dari form
  function getFormData() {
    return {
      id: editingId || Date.now().toString(),
      nama: document.getElementById("nama").value.trim(),
      email: document.getElementById("email").value.trim(),
      nomorHP: document.getElementById("nomorHP").value.trim(),
      jenisKelamin: document.querySelector('input[name="jenisKelamin"]:checked')?.value || "",
      kereta: document.getElementById("kereta").value,
      stasiunAsal: document.getElementById("stasiunAsal").value,
      stasiunTujuan: document.getElementById("stasiunTujuan").value,
      tanggalBerangkat: document.getElementById("tanggalBerangkat").value,
      kelas: document.getElementById("kelas").value,
    }
  }

  // Fungsi untuk mengisi form dengan data penumpang
  function fillFormWithData(penumpang) {
    document.getElementById("nama").value = penumpang.nama
    document.getElementById("email").value = penumpang.email
    document.getElementById("nomorHP").value = penumpang.nomorHP

    // Set jenis kelamin
    const jenisKelaminRadio = document.querySelector(`input[name="jenisKelamin"][value="${penumpang.jenisKelamin}"]`)
    if (jenisKelaminRadio) {
      jenisKelaminRadio.checked = true
    }

    // Set pilihan kereta dan stasiun
    document.getElementById("kereta").value = penumpang.kereta
    document.getElementById("stasiunAsal").value = penumpang.stasiunAsal
    document.getElementById("stasiunTujuan").value = penumpang.stasiunTujuan
    document.getElementById("tanggalBerangkat").value = penumpang.tanggalBerangkat
    document.getElementById("kelas").value = penumpang.kelas

    // Update tombol submit dan judul form
    submitButton.textContent = "Perbarui Tiket"
    formTitle.textContent = "Edit Pemesanan Tiket"
  }

  // Fungsi untuk reset form
  function resetForm() {
    form.reset()
    editingId = null
    submitButton.textContent = "Pesan Tiket"
    formTitle.textContent = "Pemesanan Tiket Baru"
  }

  // Fungsi untuk render tabel penumpang
  function renderPenumpangTable() {
    // Tampilkan atau sembunyikan tabel berdasarkan jumlah penumpang
    if (penumpangList.length > 0) {
      dataTab.disabled = false
      downloadButton.disabled = false

      // Update badge count
      if (dataTab.querySelector(".badge")) {
        dataTab.querySelector(".badge").textContent = penumpangList.length
      } else {
        const badge = document.createElement("span")
        badge.className = "badge badge-blue ml-2"
        badge.textContent = penumpangList.length
        dataTab.appendChild(badge)
      }
    } else {
      dataTab.disabled = true
      downloadButton.disabled = true

      // Remove badge if exists
      const badge = dataTab.querySelector(".badge")
      if (badge) {
        dataTab.removeChild(badge)
      }
    }

    // Kosongkan tabel
    penumpangTableBody.innerHTML = ""

    // Render data penumpang
    penumpangList.forEach((penumpang) => {
      const row = document.createElement("tr")

      // Tambahkan data ke row
      row.innerHTML = `
        <td class="px-6 py-4">
          <div>
            <div class="font-medium">${penumpang.nama}</div>
            <div class="text-sm text-gray-500">${penumpang.email}</div>
            <div class="text-sm text-gray-500">${penumpang.nomorHP}</div>
          </div>
        </td>
        <td class="px-6 py-4">${penumpang.kereta}</td>
        <td class="px-6 py-4">
          <div class="flex items-center">
            <span>${penumpang.stasiunAsal}</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-2 h-4 w-4">
              <path d="M5 12h14"></path>
              <path d="m12 5 7 7-7 7"></path>
            </svg>
            <span>${penumpang.stasiunTujuan}</span>
          </div>
        </td>
        <td class="px-6 py-4">${penumpang.tanggalBerangkat}</td>
        <td class="px-6 py-4">
          <span class="badge ${
            penumpang.kelas === "Ekonomi" ? "badge-blue" : penumpang.kelas === "Bisnis" ? "badge-purple" : "badge-amber"
          }">
            ${penumpang.kelas}
          </span>
        </td>
        <td class="px-6 py-4 text-center">
          <button class="edit-btn text-blue-600 hover:text-blue-900 mr-2 p-1 rounded-full hover:bg-blue-50" data-id="${penumpang.id}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
          </button>
          <button class="delete-btn text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50" data-id="${penumpang.id}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
        </td>
      `

      penumpangTableBody.appendChild(row)
    })

    // Tambahkan event listener untuk tombol edit dan delete
    document.querySelectorAll(".edit-btn").forEach((btn) => {
      btn.addEventListener("click", handleEdit)
    })

    document.querySelectorAll(".delete-btn").forEach((btn) => {
      btn.addEventListener("click", handleDelete)
    })
  }

  // Handler untuk submit form
  function handleSubmit(e) {
    e.preventDefault()

    if (validateForm()) {
      const formData = getFormData()

      if (editingId) {
        // Update penumpang yang sudah ada
        const index = penumpangList.findIndex((p) => p.id === editingId)
        if (index !== -1) {
          penumpangList[index] = formData
          showSuccessMessage("Data penumpang berhasil diperbarui!")
        }
      } else {
        // Tambahkan penumpang baru
        penumpangList.push(formData)
        showSuccessMessage("Pemesanan tiket berhasil!")
      }

      // Render ulang tabel
      renderPenumpangTable()

      // Reset form
      resetForm()

      // Switch to data tab if there are entries
      if (penumpangList.length > 0) {
        switchTab("data")
      }
    }
  }

  // Handler untuk edit penumpang
  function handleEdit(e) {
    const id = e.currentTarget.getAttribute("data-id")
    const penumpang = penumpangList.find((p) => p.id === id)

    if (penumpang) {
      editingId = id
      fillFormWithData(penumpang)

      // Switch to form tab
      switchTab("form")

      // Scroll ke atas
      window.scrollTo({ top: 0, behavior: "smooth" })
    }
  }

  // Handler untuk delete penumpang
  function handleDelete(e) {
    const id = e.currentTarget.getAttribute("data-id")
    penumpangList = penumpangList.filter((p) => p.id !== id)

    // Render ulang tabel
    renderPenumpangTable()

    // Reset form jika sedang mengedit penumpang yang dihapus
    if (editingId === id) {
      resetForm()
    }
  }

  // Handler untuk download data
  function handleDownload() {
    if (penumpangList.length === 0) {
      alert("Tidak ada data untuk diunduh")
      return
    }

    // Buat content CSV
    const headers = [
      "Nama",
      "Email",
      "Nomor HP",
      "Jenis Kelamin",
      "Kereta",
      "Stasiun Asal",
      "Stasiun Tujuan",
      "Tanggal Berangkat",
      "Kelas",
    ]
    const csvContent = [
      headers.join(","),
      ...penumpangList.map((p) =>
        [
          p.nama,
          p.email,
          p.nomorHP,
          p.jenisKelamin,
          p.kereta,
          p.stasiunAsal,
          p.stasiunTujuan,
          p.tanggalBerangkat,
          p.kelas,
        ].join(","),
      ),
    ].join("\n")

    // Buat file dan download
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" })
    const url = URL.createObjectURL(blob)
    const link = document.createElement("a")
    link.setAttribute("href", url)
    link.setAttribute("download", "data_penumpang.csv")
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }

  // Fungsi untuk switch tab
  function switchTab(tabName) {
    // Update active tab
    if (tabName === "form") {
      formTab.classList.add("active")
      dataTab.classList.remove("active")
      formContent.classList.remove("hidden")
      dataContent.classList.add("hidden")
    } else {
      formTab.classList.remove("active")
      dataTab.classList.add("active")
      formContent.classList.add("hidden")
      dataContent.classList.remove("hidden")
    }
  }

  // Event listeners
  form.addEventListener("submit", handleSubmit)
  downloadButton.addEventListener("click", handleDownload)

  // Tab event listeners
  formTab.addEventListener("click", () => switchTab("form"))
  dataTab.addEventListener("click", () => {
    if (!dataTab.disabled) {
      switchTab("data")
    }
  })

  // Set form tab as active by default
  formTab.classList.add("active")
})
