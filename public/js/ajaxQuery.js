$(document).ready(function(){
    $("#creation_sortie_lieu").change(function(){
        var list =$("#creation_sortie_lieu").val();
        $.ajax ({
            url : sortieLieu ,
            method : "POST",
            dataType : "json",
            data : {lieu : list}
        })
            .done(function (jsonNom, testStatus, xhr) {
            if(xhr.status == 200) {
                $('#rue').text(jsonNom.rue);
                $('#ville').text(jsonNom.ville);
                $('#codePostal').text(jsonNom.codePostal);
                $('#latitude').text(jsonNom.latitude);
                $('#longitude').text(jsonNom.longitude);
            } else {
                console.log(jsonNom.error);
            }
        })
    });
    $('#creation_sortie_lieu').change();
})

// https://forum.alsacreations.com/topic-20-65616-1-ResoluListe-deroulante-avec-affichage-des-donnees-de-la-BDD.html