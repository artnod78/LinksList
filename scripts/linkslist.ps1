cls
echo 'saisir une url'
$input=Read-Host
if($input.StartsWith('https://www.ingress.com/intel?ll=') -and $input.Contains('_')){
    $zoom=$input.Split('=')[2].Split('&')[0]

    #nous allons définir les informations culturelles sur l'anglais avant d'instancier Excel
    [System.Threading.Thread]::CurrentThread.CurrentCulture = [System.Globalization.CultureInfo] "en-US"
    #Nous pouvons, dès-lors, instancier notre objet Excel
    $Excel = New-Object -ComObject "Excel.Application"
    #Avec notre objet Excel, nous allons créer notre classeur
    $WorkBook = $Excel.Workbooks.Add()
    #Un classeur étant constitué de feuillets, ajoutons maintenant un feuillet:
    $WorkSheet = $WorkBook.WorkSheets.Add()
    $WorkSheet.Name = "LinksList"
    $WorkSheet.Select()
    #Définissons le titre de notre futur tableau en position A1
    #Le premier chiffre correspond au numéro de ligne et le deuxième chiffre au numéro de colonne
    $WorkSheet.Cells.Item(1,1) = "Ordre de tir ( à respecter scrupuleusement )"
    #Nous déclarons les entêtes de notre tableau
    $WorkSheet.Cells.Item(2,1) = "Ordre"
    $WorkSheet.Cells.Item(2,2) = "GPS Sources"
    $WorkSheet.Cells.Item(2,3) = "GPS Destination"
    $WorkSheet.Cells.Item(2,4) = "Sources"
    $WorkSheet.Cells.Item(2,5) = "Destination"
    $WorkSheet.Cells.Item(2,6) = "Intel url"
    $WorkSheet.Cells.Item(2,7) = "GMap Sources"
    $WorkSheet.Cells.Item(2,8) = "GMap Destination"
    # Compteur de ligne
    $InitialRow = 3
    
    foreach($link in $input.Split('=')[3].Split('_')){
        # Ordre
        if( $InitialRow -lt 12){
            $WorkSheet.Cells.Item($InitialRow,1) = 'Lien 0'+ ($InitialRow - 2)
        }else{
            $WorkSheet.Cells.Item($InitialRow,1) = 'Lien '+ ($InitialRow - 2)
        }
        # GPS Sources
        $WorkSheet.Cells.Item($InitialRow,2) = $link.Split(',')[0]+','+$link.Split(',')[1]
        # GPS Destination
        $WorkSheet.Cells.Item($InitialRow,3) = $link.Split(',')[2]+','+$link.Split(',')[3]
        # Emplacement reserve pour le nom des portails sources et destination
        #$WorkSheet.Cells.Item($InitialRow,4) = 
        #$WorkSheet.Cells.Item($InitialRow,5) = 
        # Intel url
        $moyx=[math]::round( ( ([Double]$link.Split(',')[0]+[Double]$link.Split(',')[2]) /2) ,6)
        $moyy=[math]::round( ( ([Double]$link.Split(',')[1]+[Double]$link.Split(',')[3]) /2) ,6)
        $WorkSheet.Cells.Item($InitialRow,6) = 'https://www.ingress.com/intel?ll='+$moyx+','+$moyy+'&z='+$zoom+'&pls='+$link
        # GMap Sources
        $WorkSheet.Cells.Item($InitialRow,7) = 'https://www.google.fr/maps/search/'+$link.Split(',')[0]+','+$link.Split(',')[1]
        # GMap Destinations
        $WorkSheet.Cells.Item($InitialRow,8) = 'https://www.google.fr/maps/search/'+$link.Split(',')[2]+','+$link.Split(',')[3]

        $InitialRow++
    }
}else{echo 'Mauvaise url!'}

#echo 'saisir un nom de fichier'
#$excelFile=Read-Host
#$WorkBook.SaveAs('X:\tonDrive\tonShare\'+$excelFile+'.xlsx')

$Excel.Visible = $true