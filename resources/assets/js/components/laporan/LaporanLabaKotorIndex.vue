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
				<li class="active">Laporan Laba Kotor Penjualan /Pelanggan</li>
			</ul>
			<div class="card">
				<div class="card-header card-header-icon" data-background-color="purple">
					<i class="material-icons">trending_up</i>
				</div>

				<div class="card-content">
					<h4 class="card-title"> Laporan Laba Kotor Penjualan /Pelanggan </h4>

					<div class="row">
						<div class="form-group col-md-2">
							<datepicker :input-class="'form-control'" placeholder="Dari Tanggal" v-model="filter.dari_tanggal" name="dari_tanggal" v-bind:id="'dari_tanggal'"></datepicker>				
						</div>
						<div class="form-group col-md-2">
							<datepicker :input-class="'form-control'" placeholder="Sampai Tanggal" v-model="filter.sampai_tanggal" name="sampai_tanggal" v-bind:id="'sampai_tanggal'"></datepicker>
						</div>
						<div class="form-group col-md-2">
							<selectize-component v-model="filter.pelanggan" :settings="placeholder_pelanggan" id="pilih_pelanggan"> 
								<option v-for="pelanggans, index in pelanggan" v-bind:value="pelanggans.id" >{{ pelanggans.nama_pelanggan }}</option>
							</selectize-component>
						</div>
						<div class="form-group col-md-2">
							<button class="btn btn-primary" id="btnSubmit" type="submit" style="margin: 0px 0px;" @click="submitLabaKotor()"><i class="material-icons">search</i> Cari</button>
						</div>
					</div>

					<div class=" table-responsive">
						<div class="pencarian">
							<input type="text" name="pencarian" v-model="pencarian_pos" placeholder="Pencarian" class="form-control">
						</div>

						<h4><b>PENJUALAN POS</b></h4>
						<table class="table table-striped table-hover">
							<thead class="text-primary">
								<tr>
									<th>No. Transaksi</th>
									<th>Waktu</th>
									<th>Pelanggan</th>
									<th style="text-align:right">Penjualan</th>
									<th style="text-align:right">Hpp</th>
									<th style="text-align:right">Laba Kotor</th>
									<th style="text-align:right">Diskon Faktur</th>
									<th style="text-align:right">Laba Jual</th>
								</tr>
							</thead>
							<tbody v-if="labaKotor.length > 0 && loading == false"  class="data-ada">
								<tr v-for="labaKotors, index in labaKotor" >

									<td>{{ labaKotors.laba_kotor.id }}</td>
									<td>{{ labaKotors.laba_kotor.created_at | tanggal }}</td>
									<td>{{ labaKotors.pelanggan }}</td>
									<td align="right">{{ labaKotors.total | pemisahTitik }}</td>
									<td align="right">{{ labaKotors.hpp | pemisahTitik }}</td>
									<td align="right">{{ labaKotors.total_laba_kotor | pemisahTitik }}</td>
									<td align="right">{{ labaKotors.laba_kotor.potongan | pemisahTitik }}</td>
									<td align="right">{{ labaKotors.laba_jual | pemisahTitik }}</td>

								</tr>

								<tr style="color:red">

									<td>TOTAL</td>
									<td></td>
									<td></td>
									<td align="right">{{ subtotalLabaKotor.subtotal_penjualan | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotor.subtotal_hpp | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotor.subtotal_laba_kotor | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotor.subtotal_potongan | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotor.subtotal_laba_jual | pemisahTitik }}</td>

								</tr>
							</tbody>					
							<tbody class="data-tidak-ada" v-else-if="labaKotor.length == 0 && loading == false">
								<tr ><td colspan="8"  class="text-center">Tidak Ada Data</td></tr>
							</tbody>
						</table>
					</div><!--RESPONSIVE-->
					<vue-simple-spinner v-if="loading"></vue-simple-spinner>
					<div align="right"><pagination :data="labaKotorData" v-on:pagination-change-page="prosesLaporan" :limit="4"></pagination></div>
					<hr style="margin-top: 30px; margin-bottom: 15px; border-top: 5px solid #eeeeee;">

					<div class=" table-responsive">
						<div class="pencarian">
							<input type="text" name="pencarian" v-model="pencarian_pesanan" placeholder="Pencarian" class="form-control">
						</div>

						<h4><b>PENJUALAN ONLINE</b></h4>
						<table class="table table-striped table-hover">
							<thead class="text-primary">
								<tr>
									<th>No. Transaksi</th>
									<th>Waktu</th>
									<th>Pelanggan</th>
									<th style="text-align:right">Penjualan</th>
									<th style="text-align:right">Hpp</th>
									<th style="text-align:right">Laba Kotor</th>
									<th style="text-align:right">Diskon Faktur</th>
									<th style="text-align:right">Laba Jual</th>
								</tr>
							</thead>
							<tbody v-if="labaKotorPesanan.length > 0 && loadingPesanan == false"  class="data-ada">
								<tr v-for="labaKotorPesanans, index in labaKotorPesanan" >

									<td>{{ labaKotorPesanans.laba_kotor.id }}</td>
									<td>{{ labaKotorPesanans.laba_kotor.created_at | tanggal }}</td>
									<td>{{ labaKotorPesanans.laba_kotor.name }}</td>
									<td align="right">{{ labaKotorPesanans.total | pemisahTitik }}</td>
									<td align="right">{{ labaKotorPesanans.hpp | pemisahTitik }}</td>
									<td align="right">{{ labaKotorPesanans.total_laba_kotor | pemisahTitik }}</td>
									<td align="right">0</td>
									<td align="right">{{ labaKotorPesanans.laba_jual | pemisahTitik }}</td>

								</tr>

								<tr style="color:red">

									<td>TOTAL</td>
									<td></td>
									<td></td>
									<td align="right">{{ subtotalLabaKotorPesanan.subtotal_penjualan | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotorPesanan.subtotal_hpp | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotorPesanan.subtotal_laba_kotor | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotorPesanan.subtotal_potongan | pemisahTitik }}</td>
									<td align="right">{{ subtotalLabaKotorPesanan.subtotal_laba_jual | pemisahTitik }}</td>

								</tr>

								<tr v-if="labaKotorPesanan.length > 0 && labaKotor.length > 0 && loadingAkhir == false"  style="color:red; font-weight:bold">

									<td>TOTAL KESELURUHAN</td>
									<td></td>
									<td></td>
									<td align="right">{{ totalAkhir.total_penjualan | pemisahTitik }}</td>
									<td align="right">{{ totalAkhir.total_hpp | pemisahTitik }}</td>
									<td align="right">{{ totalAkhir.total_laba_kotor | pemisahTitik }}</td>
									<td align="right">{{ totalAkhir.total_potongan | pemisahTitik }}</td>
									<td align="right">{{ totalAkhir.total_laba_jual | pemisahTitik }}</td>

								</tr>
							</tbody>					
							<tbody class="data-tidak-ada" v-else-if="labaKotorPesanan.length == 0 && loadingPesanan == false">
								<tr ><td colspan="8"  class="text-center">Tidak Ada Data</td></tr>
							</tbody>
						</table>
					</div><!--RESPONSIVE-->

					<!--DOWNLOAD EXCEL-->
					<a href="#" class='btn btn-warning' id="btnExcel" target='blank' :style="'display: none'"><i class="material-icons">file_download</i> Download Excel</a>

					<!--CETAK LAPORAN-->
					<a href="#" class='btn btn-success' id="btnCetak" target='blank' :style="'display: none'"><i class="material-icons">print</i> Cetak Laporan</a>

					<vue-simple-spinner v-if="loadingPesanan"></vue-simple-spinner>
					<div align="right"><pagination :data="labaKotorPesananData" v-on:pagination-change-page="prosesLaporanPesanan" :limit="4"></pagination></div>
				</div>
			</div>
		</div>
	</div>
	

</template>


<script>
import { mapState } from 'vuex';
	export default {
		data: function () {
			return {
				labaKotor: [],
				labaKotorData: {},
				subtotalLabaKotor: {},
				labaKotorPesanan: [],
				labaKotorPesananData: {},
				subtotalLabaKotorPesanan: {},
				totalAkhir: {},
				filter: {
					dari_tanggal: '',
					sampai_tanggal: new Date(),
					pelanggan: '',
				},
				url : window.location.origin+(window.location.pathname).replace("dashboard", "laporan-laba-kotor"),
				urlDownloadExcel : window.location.origin+(window.location.pathname).replace("dashboard", "laporan-laba-kotor/download-excel-laba-kotor"),
				urlCetak : window.location.origin+(window.location.pathname).replace("dashboard", "laporan-laba-kotor/cetak-laporan"),
				pencarian_pos: '',
				pencarian_pesanan: '',
				placeholder_pelanggan: {
					placeholder: '--PILIH PELANGGAN--'
				},
				loading: false,
				loadingPesanan: false,
				loadingAkhir: false,
			}
		},
		mounted() {
			var app = this;
			app.$store.dispatch('LOAD_PELANGGAN_LIST');
			var awal_tanggal = new Date();
			awal_tanggal.setDate(1);
			app.filter.dari_tanggal = awal_tanggal;

		},
		computed : mapState ({    
	      pelanggan(){
	        return this.$store.state.pelanggan
	      }
	    }),
		watch: {
        // whenever question changes, this function will run
        pencarian_pos: function (newQuestion) {
        	this.getHasilPencarianPos();
        },
        pencarian_pesanan: function (newQuestion) {
        	this.getHasilPencarianPesanan();
        }
    },
    filters: {
    	pemisahTitik: function (value) {
    		var angka = [value];
    		var numberFormat = new Intl.NumberFormat('es-ES');
    		var formatted = angka.map(numberFormat.format);
    		return formatted.join('; ');
    	},
    	tanggal: function (value) {
    		return moment(String(value)).format('DD/MM/YYYY hh:mm')
    	}
    },
    methods: {
    	submitLabaKotor(){
    		var app = this;
    		app.prosesLaporan();
    		app.prosesLaporanPesanan();
    		app.totalLabaKotor();
    		app.totalLabaKotorPesanan();
    		app.totalAkhirLabaKotor();
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
    			app.labaKotor = resp.data.data;
    			app.labaKotorData = resp.data;
    			app.loading = false
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Laporan Laba Kotor Penjualan /Pelanggan");
    		});
    	},
    	prosesLaporanPesanan(page) {
    		var app = this;	
    		var newFilter = app.filter;
    		if (typeof page === 'undefined') {
    			page = 1;
    		}
    		app.loadingPesanan = true,
    		axios.post(app.url+'/view-pesanan?page='+page, newFilter)
    		.then(function (resp) {
    			app.labaKotorPesanan = resp.data.data;
    			app.labaKotorPesananData = resp.data;
    			app.loadingPesanan = false
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Laporan Laba Kotor Penjualan /Pelanggan");
    		});
    	},
    	getHasilPencarianPos(page){
    		var app = this;
    		var newFilter = app.filter;
    		if (typeof page === 'undefined') {
    			page = 1;
    		}
    		axios.post(app.url+'/pencarian?search='+app.pencarian_pos+'&page='+page, newFilter)
    		.then(function (resp) {
    			app.labaKotor = resp.data.data;
    			app.labaKotorData = resp.data;
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Laporan Laba Kotor Penjualan /Pelanggan");
    		});
    	},
    	getHasilPencarianPesanan(page){
    		var app = this;
    		var newFilter = app.filter;
    		if (typeof page === 'undefined') {
    			page = 1;
    		}
    		axios.post(app.url+'/pencarian-pesanan?search='+app.pencarian_pesanan+'&page='+page, newFilter)
    		.then(function (resp) {
    			app.labaKotorPesanan = resp.data.data;
    			app.labaKotorPesananData = resp.data;
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Laporan Laba Kotor Penjualan /Pelanggan");
    		});
    	},
    	totalLabaKotor() {
    		var app = this;	
    		var newFilter = app.filter;

    		app.loading = true,
    		axios.post(app.url+'/subtotal-laba-kotor', newFilter)
    		.then(function (resp) {
    			app.subtotalLabaKotor = resp.data;
    			app.loading = false
    			console.log(resp.data)
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Subtotal Laba Kotor");
    		});
    	},
    	totalLabaKotorPesanan() {
    		var app = this;	
    		var newFilter = app.filter;

    		app.loading = true,
    		axios.post(app.url+'/subtotal-laba-kotor-pesanan', newFilter)
    		.then(function (resp) {
    			app.subtotalLabaKotorPesanan = resp.data;
    			app.loadingPesanan = false
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Subtotal Laba Kotor");
    		});
    	},
    	totalAkhirLabaKotor() {
    		var app = this;	
    		var newFilter = app.filter;

    		app.loadingAkhir = true,
    		axios.post(app.url+'/total-akhir-laba-kotor', newFilter)
    		.then(function (resp) {
    			app.totalAkhir = resp.data;
    			app.loadingAkhir = false;
    			console.log(resp.data);
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Subtotal Laba Kotor");
    		});
    	},
    	downloadExcel() {
    		var app = this;	
    		var newFilter = app.filter;
    		if (newFilter.pelanggan == "" || newFilter.pelanggan == null) {
    			newFilter.pelanggan = 0;
    		}
    		axios.get(app.urlDownloadExcel+'/'+newFilter.dari_tanggal+'/'+newFilter.sampai_tanggal+'/'+newFilter.pelanggan)
    		.then(function (resp) {
    		})
    		.catch(function (resp) {
    			console.log(resp);
    			alert("Tidak Dapat Memuat Subtotal Laba Kotor");
    		});
    	},
    	showButton() {
    		var app = this;
    		var filter = app.filter;

    		if (filter.pelanggan == "") {
    			filter.pelanggan = "semua";
    		};

    		var date_dari_tanggal = filter.dari_tanggal;
    		var date_sampai_tanggal = filter.sampai_tanggal;
    		var dari_tanggal = "" + date_dari_tanggal.getFullYear() +'-'+ ((date_dari_tanggal.getMonth() + 1) > 9 ? '' : '0') + (date_dari_tanggal.getMonth() + 1) +'-'+ (date_dari_tanggal.getDate() > 9 ? '' : '0') + date_dari_tanggal.getDate();
    		var sampai_tanggal = "" + date_sampai_tanggal.getFullYear() +'-'+ ((date_sampai_tanggal.getMonth() + 1) > 9 ? '' : '0') + (date_sampai_tanggal.getMonth() + 1) +'-'+ (date_sampai_tanggal.getDate() > 9 ? '' : '0') + date_sampai_tanggal.getDate();


    		$("#btnExcel").show();
    		$("#btnCetak").show();
    		$("#btnExcel").attr('href', app.urlDownloadExcel+'/'+dari_tanggal+'/'+sampai_tanggal+'/'+filter.pelanggan);
    		$("#btnCetak").attr('href', app.urlCetak+'/'+dari_tanggal+'/'+sampai_tanggal+'/'+filter.pelanggan);  
    	}
    }
}
</script>