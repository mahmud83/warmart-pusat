<style scoped>
.pencarian {
	color: red; 
	float: right;
	padding-bottom: 10px;
}
</style>

<template>
	<div class="row">
		<div class="col-md-12">
			<ul class="breadcrumb">
				<li><router-link :to="{name: 'indexDashboard'}">Home</router-link></li>
				<li class="active">Laporan Penjualan /Pelanggan</li>
			</ul>
			<div class="card">
				<div class="card-header card-header-icon" data-background-color="purple">
					<i class="material-icons">library_books</i>
				</div>

				<div class="card-content">
					<h4 class="card-title"> Laporan Penjualan Pos /Pelanggan </h4>

					<div class="row">
						<div class="form-group col-md-2">
							<datepicker :input-class="'form-control'" placeholder="Dari Tanggal" v-model="filter.dari_tanggal" name="dari_tanggal" v-bind:id="'dari_tanggal'"></datepicker>				
						</div>
						<div class="form-group col-md-2">
							<datepicker :input-class="'form-control'" placeholder="Sampai Tanggal" v-model="filter.sampai_tanggal" name="sampai_tanggal" v-bind:id="'sampai_tanggal'"></datepicker>
						</div>
						<div class="form-group col-md-2">
							<selectize-component v-model="filter.pelanggan"  id="pilih_pelanggan"> 
								<option v-for="pelanggans, index in pelanggan" v-bind:value="pelanggans.id" >{{ pelanggans.nama_pelanggan }}</option>
							</selectize-component>
						</div>
						<div class="form-group col-md-2">
							<selectize-component v-model="filter.kasir"  id="pilih_kasir"> 
								<option v-for="kasirs, index in kasir" v-bind:value="kasirs.id" >{{ kasirs.nama_kasir }}</option>
							</selectize-component>
						</div>

						<div class="form-group col-md-2">
							<button class="btn btn-primary" id="btnSubmit" type="submit" style="margin: 0px 0px;" @click="submitPenjualanPelanggan()"><i class="material-icons">search</i> Cari</button>
						</div>
					</div>

					<div class=" table-responsive">
						<div class="pencarian">
							<input type="text" name="pencarian" v-model="pencarian" placeholder="Pencarian" class="form-control">
						</div>

						<table class="table table-striped table-hover">
							<thead class="text-primary">
								<tr>
									<th>Kode Produk</th>
									<th>Nama Pelanggan</th>
									<th>Nama Produk</th>
									<th style="text-align:right">Jumlah</th>
									<th>Satuan</th>
									<th style="text-align:right">Subtotal</th>
									<th style="text-align:right">Diskon</th>
									<th style="text-align:right">Pajak</th>
									<th style="text-align:right">Total</th>
								</tr>
							</thead>
							<tbody v-if="penjualanPelanggan.length > 0 && loading == false"  class="data-ada">
								<tr v-for="penjualanPelanggans, index in penjualanPelanggan" >

									<td>{{ penjualanPelanggans.laporan_penjualans.kode_barang }}</td>
									<td>{{ penjualanPelanggans.pelanggan }}</td>
									<td>{{ penjualanPelanggans.laporan_penjualans.nama_barang }}</td>
									<td align="right">{{ penjualanPelanggans.laporan_penjualans.jumlah_produk | pemisahTitik }}</td>
									<td>{{ penjualanPelanggans.laporan_penjualans.nama_satuan }}</td>
									<td align="right">{{ penjualanPelanggans.laporan_penjualans.subtotal | pemisahTitik }}</td>
									<td align="right">{{ penjualanPelanggans.laporan_penjualans.potongan | pemisahTitik }}</td>
									<td align="right">{{ penjualanPelanggans.laporan_penjualans.tax | pemisahTitik }}</td>
									<td align="right">{{ penjualanPelanggans.sub_total | pemisahTitik }}</td>

								</tr>

								<tr style="color:red">
									<td>TOTAL</td>
									<td></td>
									<td></td>
									<td align="right">{{ totalPenjualanPosPelanggan.jumlah_produk | pemisahTitik }}</td>
									<td></td>
									<td align="right">{{ totalPenjualanPosPelanggan.subtotal | pemisahTitik }}</td>
									<td></td>
									<td></td>
									<td align="right">{{ totalPenjualanPosPelanggan.total | pemisahTitik }}</td>
								</tr>
							</tbody>					
							<tbody class="data-tidak-ada" v-else-if="penjualanPelanggan.length == 0 && loading == false">
								<tr ><td colspan="9"  class="text-center">Tidak Ada Data</td></tr>
							</tbody>
						</table>
					</div><!--RESPONSIVE-->

					<vue-simple-spinner v-if="loading"></vue-simple-spinner>
					<div align="right"><pagination :data="penjualanPelangganData" v-on:pagination-change-page="prosesLaporan" :limit="4"></pagination></div>
				</div>
				<hr>
				<!-- laporan penjualan online per peroduk -->
				<div class="card-content">
					<h4 class="card-title"> Laporan Penjualan Online /Pelanggan </h4>

					<div class=" table-responsive">
						<div class="pencarian">
							<input type="text" name="pencarianOnline" v-model="pencarianOnline" placeholder="Pencarian" class="form-control">
						</div>

						<table class="table table-striped table-hover">
							<thead class="text-primary">
								<tr>
									<th>Kode Produk</th>
									<th>Nama Pelanggan</th>
									<th>Nama Produk</th>
									<th style="text-align:right">Harga</th>
									<th style="text-align:right">Jumlah</th>
									<th style="text-align:right">Subtotal</th>
									<th style="text-align:right">Diskon</th>
									<th style="text-align:right">Total</th>
								</tr>
							</thead>
							<tbody v-if="penjualanOnlinePelanggan.length > 0 && loading == false"  class="data-ada">
								<tr v-for="penjualanOnlinePelanggans, index in penjualanOnlinePelanggan" >
									<td>{{ penjualanOnlinePelanggans.laporan_penjualan_online.kode_barang }}</td>
									<td>{{ penjualanOnlinePelanggans.laporan_penjualan_online.name }}</td>
									<td>{{ penjualanOnlinePelanggans.laporan_penjualan_online.nama_barang }}</td>
									<td align="right">{{ penjualanOnlinePelanggans.laporan_penjualan_online.harga | pemisahTitik }}</td>
									<td align="right">{{ penjualanOnlinePelanggans.laporan_penjualan_online.jumlah | pemisahTitik }}</td>
									<td align="right">{{ penjualanOnlinePelanggans.laporan_penjualan_online.total | pemisahTitik }}</td>
									<td align="right">{{ penjualanOnlinePelanggans.laporan_penjualan_online.potongan | pemisahTitik }}</td>
									<td align="right">{{ penjualanOnlinePelanggans.laporan_penjualan_online.subtotal | pemisahTitik }}</td>

								</tr>

								<tr style="color:red">
									<td>TOTAL</td>
									<td></td>
									<td></td>
									<td align="right">{{ totalLaporanPenjualanOnlinePelanggan.harga | pemisahTitik }}</td>
									<td align="right">{{ totalLaporanPenjualanOnlinePelanggan.jumlah | pemisahTitik }}</td>
									<td align="right">{{ totalLaporanPenjualanOnlinePelanggan.total | pemisahTitik }}</td>
									<td></td>
									<td align="right">{{ totalLaporanPenjualanOnlinePelanggan.subtotal | pemisahTitik }}</td>
								</tr>
							</tbody>					
							<tbody class="data-tidak-ada" v-else-if="penjualanOnlinePelanggan.length == 0 && loading == false">
								<tr ><td colspan="9"  class="text-center">Tidak Ada Data</td></tr>
							</tbody>
						</table>
					</div><!--RESPONSIVE-->

					<hr>
					<!--DOWNLOAD EXCEL-->
					<a href="#" class='btn btn-warning' id="btnExcel" target='blank' :style="'display: none'"><i class="material-icons">file_download</i> Download Excel</a>

					<!--CETAK LAPORAN-->
					<a href="#" class='btn btn-success' id="btnCetak" target='blank' :style="'display: none'"><i class="material-icons">print</i> Cetak Laporan</a>

					<vue-simple-spinner v-if="loading"></vue-simple-spinner>
					<div align="right"><pagination :data="penjualanOnlinePelangganData" v-on:pagination-change-page="prosesLaporan" :limit="4"></pagination></div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
import { mapState } from 'vuex';
export default {
	data: function () {
		return {

			penjualanPelanggan: [],
			penjualanOnlinePelanggan:[],
			penjualanPelangganData: {},
			penjualanOnlinePelangganData: {},
			totalPenjualanPosPelanggan: {},
			totalLaporanPenjualanOnlinePelanggan: {},
			filter: {
				dari_tanggal: '',
				sampai_tanggal: new Date(),
				pelanggan: '',
				kasir: '',
			},
			url : window.location.origin+(window.location.pathname).replace("dashboard", "laporan-penjualan-pelanggan"),
			urlDownloadExcel : window.location.origin+(window.location.pathname).replace("dashboard", "laporan-penjualan-pelanggan/download-excel-penjualan-pelanggan"),
			urlCetak : window.location.origin+(window.location.pathname).replace("dashboard", "laporan-penjualan-pelanggan/cetak-laporan"),
			pencarian: '',
			pencarianOnline: '',
			loading: false,

		}
	},
	mounted() {
		var app = this;
		var awal_tanggal = new Date();
		awal_tanggal.setDate(1);
		app.$store.dispatch('LOAD_PELANGGAN_LIST');
		app.$store.dispatch('LOAD_KASIR_LIST');
		app.filter.dari_tanggal = awal_tanggal;
	},
	computed : mapState ({    
      pelanggan(){
        return this.$store.state.pelanggan
      },
       kasir(){
        return this.$store.state.kasir
      }
    }),
	watch: {
// whenever question changes, this function will run
pencarian: function (newQuestion) {
	this.getHasilPencarian();
},
pencarianOnline: function (newQuestion) {
	this.getHasilPencarianOnline();
}
},
filters: {
	pemisahTitik: function (value) {
		var angka = [value];
		var numberFormat = new Intl.NumberFormat('es-ES');
		var formatted = angka.map(numberFormat.format);
		return formatted.join('; ');
	}
},
methods: {
	submitPenjualanPelanggan(){
		var app = this;
		var filter = app.filter;
		app.prosesLaporan();
		app.prosesLaporanOnline();
		app.totalPenjualanPelanggan();
		app.totalPenjualanOnlinePelanggan();
		app.showButton();   		
	},
	prosesLaporan(page) {
		var app = this;	
		var newFilter = app.filter;
		if (typeof page === 'undefined') {
			page = 1;
		}
		app.loading = true,
		axios.post(app.url+'/view?page='+page, newFilter)
		.then(function (resp) {
			app.penjualanPelanggan = resp.data.data;
			app.penjualanpelangganData = resp.data;
			app.loading = false
			console.log(resp.data.data);
		})
		.catch(function (resp) {
			// console.log(resp);
			alert("Tidak Dapat Memuat Laporan Penjualan Pos /pelanggan");
		});
	},prosesLaporanOnline(page) {
		var app = this;	
		var newFilter = app.filter;
		if (typeof page === 'undefined') {
			page = 1;
		}
		app.loading = true,
		axios.post(app.url+'/view-online?page='+page, newFilter)
		.then(function (resp) {
			app.penjualanOnlinePelanggan = resp.data.data;
			app.penjualanOnlinePelangganData = resp.data;
			app.loading = false
			console.log(resp.data.data);
		})
		.catch(function (resp) {
			// console.log(resp);
			alert("Tidak Dapat Memuat Laporan Penjualan Online /pelanggan");
		});
	},
	getHasilPencarian(page){
		var app = this;
		var newFilter = app.filter;
		if (typeof page === 'undefined') {
			page = 1;
		}
		axios.post(app.url+'/pencarian?search='+app.pencarian+'&page='+page, newFilter)
		.then(function (resp) {
			app.penjualanPelanggan = resp.data.data;
			app.penjualanpelangganData = resp.data;
		})
		.catch(function (resp) {
			// console.log(resp);
			alert("Tidak Dapat Memuat Laporan Penjualan Pos /pelanggan");
		});
	},
	getHasilPencarianOnline(page){
		var app = this;
		var newFilter = app.filter;
		if (typeof page === 'undefined') {
			page = 1;
		}
		axios.post(app.url+'/pencarian-online?search='+app.pencarianOnline+'&page='+page, newFilter)
		.then(function (resp) {
			app.penjualanOnlinePelanggan = resp.data.data;
			app.penjualanOnlinePelangganData = resp.data;
		})
		.catch(function (resp) {
			// console.log(resp);
			alert("Tidak Dapat Memuat Laporan Penjualan Pos /pelanggan");
		});
	},
	totalPenjualanPelanggan() {
		var app = this;	
		var newFilter = app.filter;

		app.loading = true,
		axios.post(app.url+'/total-penjualan-pos-pelanggan', newFilter)
		.then(function (resp) {
			app.totalPenjualanPosPelanggan = resp.data;
			app.loading = false
			console.log(resp.data);    			
		})
		.catch(function (resp) {
			// console.log(resp);
			alert("Tidak Dapat Memuat Subtotal Mutasi Stok");
		});
	},	totalPenjualanOnlinePelanggan() {
		var app = this;	
		var newFilter = app.filter;

		app.loading = true,
		axios.post(app.url+'/total-penjualan-online-pelanggan', newFilter)
		.then(function (resp) {
			app.totalLaporanPenjualanOnlinePelanggan = resp.data;
			app.loading = false
			console.log(resp.data);    			
		})
		.catch(function (resp) {
			// console.log(resp);
			alert("Tidak Dapat Memuat Total Penjualan Online /pelanggan");
		});
	},	
	showButton() {
		var app = this;
		var filter = app.filter;

		if (filter.pelanggan == "") {
			filter.pelanggan = "semua";
		};
		if (filter.kasir == "") {
			filter.kasir = 0;
		};

		var date_dari_tanggal = filter.dari_tanggal;
		var date_sampai_tanggal = filter.sampai_tanggal;
		var dari_tanggal = "" + date_dari_tanggal.getFullYear() +'-'+ ((date_dari_tanggal.getMonth() + 1) > 9 ? '' : '0') + (date_dari_tanggal.getMonth() + 1) +'-'+ (date_dari_tanggal.getDate() > 9 ? '' : '0') + date_dari_tanggal.getDate();
		var sampai_tanggal = "" + date_sampai_tanggal.getFullYear() +'-'+ ((date_sampai_tanggal.getMonth() + 1) > 9 ? '' : '0') + (date_sampai_tanggal.getMonth() + 1) +'-'+ (date_sampai_tanggal.getDate() > 9 ? '' : '0') + date_sampai_tanggal.getDate();

		$("#btnExcel").show();
		$("#btnCetak").show();
		$("#btnExcel").attr('href', app.urlDownloadExcel+'/'+dari_tanggal+'/'+sampai_tanggal+'/'+filter.pelanggan+'/'+filter.kasir);
		$("#btnCetak").attr('href', app.urlCetak+'/'+dari_tanggal+'/'+sampai_tanggal+'/'+filter.pelanggan+'/'+filter.kasir);
	}
}
}
</script>