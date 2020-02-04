<template>
  <div>
    <br>
    <br>
    <sui-card class="ui centered card" style="width: 1000px">
      <sui-card-content>
        <sui-card-header>
          Rénommer Les Pdfs
        </sui-card-header>
      </sui-card-content>
      <sui-card-content>
        <sui-header size="tiny">DOSSIERS</sui-header>
        <sui-form  id="form_pdf" @submit="renamepdf" method="post">
         <sui-grid :columns="2">
          <sui-grid-column>
            <sui-form-field>
              <sui-label pointing="below">Dossier Cible  (noms non corrects)</sui-label>
              <input type="text" v-model="dossier_cible" placeholder="E:\Scans\dossiercible">
            </sui-form-field>
          </sui-grid-column>
          <sui-grid-column>
            <sui-form-field>
              <sui-label pointing="below">Excel de Reference (noms corrects)</sui-label>
              <input type="file" id="fileexcel"  ref="fileexcel"  @change="getfile()" placeholder="E:\Scans\excel.xls">
            </sui-form-field>
          </sui-grid-column>
        </sui-grid>
        <br>
        <br>
        <sui-grid>
          <sui-grid-column textAlign="center">
            <sui-button primary size="large" type="submit"> lancer operation</sui-button>
          </sui-grid-column> 
        </sui-grid>
      </sui-form>
      <br>

      <!--Loader -->
      <sui-grid :columns="1"  v-model="en_cours" v-if="en_cours">
        <sui-grid-column >
          <sui-segment>
            <br>
            <br>
            <br>
            <sui-dimmer active inverted centered>
             <sui-loader indeterminate size="large">Renommage de Fichier en cours ...</sui-loader>
           </sui-dimmer>
           <br>
           <br>
         </sui-segment>
       </sui-grid-column>
     </sui-grid>
     <sui-grid :columns="2">
      <sui-grid-column>
        <sui-table celled v-if="trouver">
          <sui-table-header>
            <sui-table-row>
              <sui-table-header-cell>#</sui-table-header-cell>
              <sui-table-header-cell>pdf Trouver</sui-table-header-cell>
            </sui-table-row>
          </sui-table-header>

          <sui-table-body v-model="les_pdfs_trouver" v-for="elt in les_pdfs_trouver" v-bind:key="elt.id_pdf">
            <sui-table-row>
              <sui-table-cell>
                <sui-label ribbon>#</sui-label>
              </sui-table-cell>
              <sui-table-cell>{{elt.nom_pdf}}</sui-table-cell>
            </sui-table-row>
          </sui-table-body>
        </sui-table>

      </sui-grid-column>
      <sui-grid-column>
        <sui-table celled v-if="non_trouver">
          <sui-table-header>
            <sui-table-row>
              <sui-table-header-cell>#</sui-table-header-cell>
              <sui-table-header-cell>pdfs Non Trouver</sui-table-header-cell>
            </sui-table-row>
          </sui-table-header>

          <sui-table-body  v-model="les_pdfs_non_trouver" v-for="elt in les_pdfs_non_trouver" v-bind:key="elt.id_pdf">
            <sui-table-row>
              <sui-table-cell>
                <sui-label ribbon>#</sui-label>
              </sui-table-cell>
              <sui-table-cell>{{elt.id_pdf}}</sui-table-cell>
            </sui-table-row>
          </sui-table-body>
        </sui-table>
      </sui-grid-column>
    </sui-grid>
  </sui-card-content>
</sui-card>
</div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      dossier_cible :'',
      exel_ref :'',
      les_pdfs_trouver: [],
      les_pdfs_non_trouver: [],
      trouver: false,
      non_trouver: false ,
      en_cours: false ,
      errors: []
    }
  },
  methods: {

    getfile(){
      this.exel_ref = document.getElementById('fileexcel').files[0]
    },

    renamepdf(e){
      e.preventDefault()
      var routeUrl = route('renamepdf')
      //console.log('je suis ici')
      let data = new FormData();
      let fichierxcel = this.exel_ref
      console.log(this.exel_ref)
      data.append('fichierexcel', fichierxcel)
      data.append('nom_fichier',fichierxcel.name);
      data.append('dossier_cible',this.dossier_cible);
      //data.append('_method', 'POST');
      this.en_cours =true ;
      axios.post(routeUrl,data)
      .then(response => {
        //console.log(response.data)
        let Laraveldata = response.data
        this.les_pdfs_trouver =response.data.pdfs_trouver
        console.log(response.data.pdfs_trouver)
        this.les_pdfs_non_trouver =response.data.pdfs_non_trouver
        this.trouver =true
        this.non_trouver =true
        this.en_cours =false 
      }).catch(e => {
        this.en_cours =false 
        //this.errors.push(e)
        console.log(e)
      })
      //console.log('renome ok')
    }
  }
}
</script>
