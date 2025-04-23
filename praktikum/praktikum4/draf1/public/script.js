document.addEventListener("DOMContentLoaded", () => {
    // Elemen-elemen form
    const form = document.getElementById("registrationForm")
    const successAlert = document.getElementById("successAlert")
    const successMessage = document.getElementById("successMessage")
    const dataPeserta = document.getElementById("dataPeserta")
    const pesertaTableBody = document.getElementById("pesertaTableBody")
    const downloadButton = document.getElementById("downloadButton")
    const submitButton = document.getElementById("submitButton")
  
    // Array untuk menyimpan data peserta
    let pesertaList = []
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
  
      // Validasi Pilihan
      const pilihan = document.getElementById("pilihan").value
      if (!pilihan) {
        showError("pilihan", "Pilihan harus dipilih")
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
        pilihan: document.getElementById("pilihan").value,
      }
    }
  
    // Fungsi untuk mengisi form dengan data peserta
    function fillFormWithData(peserta) {
      document.getElementById("nama").value = peserta.nama
      document.getElementById("email").value = peserta.email
      document.getElementById("nomorHP").value = peserta.nomorHP
  
      // Set jenis kelamin
      const jenisKelaminRadio = document.querySelector(`input[name="jenisKelamin"][value="${peserta.jenisKelamin}"]`)
      if (jenisKelaminRadio) {
        jenisKelaminRadio.checked = true
      }
  
      // Set pilihan
      document.getElementById("pilihan").value = peserta.pilihan
  
      // Update tombol submit
      submitButton.textContent = "Perbarui"
    }
  
    // Fungsi untuk reset form
    function resetForm() {
      form.reset()
      editingId = null
      submitButton.textContent = "Daftar"
    }
  
    // Fungsi untuk render tabel peserta
    function renderPesertaTable() {
      // Tampilkan atau sembunyikan tabel berdasarkan jumlah peserta
      if (pesertaList.length > 0) {
        dataPeserta.classList.remove("hidden")
        downloadButton.disabled = false
      } else {
        dataPeserta.classList.add("hidden")
        downloadButton.disabled = true
      }
  
      // Kosongkan tabel
      pesertaTableBody.innerHTML = ""
  
      // Render data peserta
      pesertaList.forEach((peserta) => {
        const row = document.createElement("tr")
  
        // Tambahkan data ke row
        row.innerHTML = `
          <td class="px-6 py-4 whitespace-nowrap">${peserta.nama}</td>
          <td class="px-6 py-4 whitespace-nowrap">${peserta.email}</td>
          <td class="px-6 py-4 whitespace-nowrap">${peserta.nomorHP}</td>
          <td class="px-6 py-4 whitespace-nowrap">${peserta.jenisKelamin}</td>
          <td class="px-6 py-4 whitespace-nowrap">${peserta.pilihan}</td>
          <td class="px-6 py-4 whitespace-nowrap text-center">
            <button class="edit-btn text-blue-600 hover:text-blue-900 mr-2" data-id="${peserta.id}">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
            </button>
            <button class="delete-btn text-red-600 hover:text-red-900" data-id="${peserta.id}">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </td>
        `
  
        pesertaTableBody.appendChild(row)
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
          // Update peserta yang sudah ada
          const index = pesertaList.findIndex((p) => p.id === editingId)
          if (index !== -1) {
            pesertaList[index] = formData
            showSuccessMessage("Data peserta berhasil diperbarui!")
          }
        } else {
          // Tambahkan peserta baru
          pesertaList.push(formData)
          showSuccessMessage("Pendaftaran berhasil!")
        }
  
        // Render ulang tabel
        renderPesertaTable()
  
        // Reset form
        resetForm()
      }
    }
  
    // Handler untuk edit peserta
    function handleEdit(e) {
      const id = e.currentTarget.getAttribute("data-id")
      const peserta = pesertaList.find((p) => p.id === id)
  
      if (peserta) {
        editingId = id
        fillFormWithData(peserta)
  
        // Scroll ke atas
        window.scrollTo({ top: 0, behavior: "smooth" })
      }
    }
  
    // Handler untuk delete peserta
    function handleDelete(e) {
      const id = e.currentTarget.getAttribute("data-id")
      pesertaList = pesertaList.filter((p) => p.id !== id)
  
      // Render ulang tabel
      renderPesertaTable()
  
      // Reset form jika sedang mengedit peserta yang dihapus
      if (editingId === id) {
        resetForm()
      }
    }
  
    // Handler untuk download data
    function handleDownload() {
      if (pesertaList.length === 0) {
        alert("Tidak ada data untuk diunduh")
        return
      }
  
      // Buat content CSV
      const headers = ["Nama", "Email", "Nomor HP", "Jenis Kelamin", "Pilihan"]
      const csvContent = [
        headers.join(","),
        ...pesertaList.map((p) => [p.nama, p.email, p.nomorHP, p.jenisKelamin, p.pilihan].join(",")),
      ].join("\n")
  
      // Buat file dan download
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" })
      const url = URL.createObjectURL(blob)
      const link = document.createElement("a")
      link.setAttribute("href", url)
      link.setAttribute("download", "data_peserta.csv")
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    }
  
    // Event listeners
    form.addEventListener("submit", handleSubmit)
    downloadButton.addEventListener("click", handleDownload)
  })
  