import xlsxwriter

print('saisir une url')
intelurl = input()
if intelurl.startswith('https://www.ingress.com/intel?ll=') and intelurl.find('_'):
	workbook = xlsxwriter.Workbook('hello.xlsx')
	worksheet = workbook.add_worksheet('Liste des links')
	worksheet.write(0, 0, 'Ordre de tir ( a respecter scrupuleusement )')
	worksheet.write(1, 0, 'Ordre')
	worksheet.write(1, 1, 'GPS Sources')
	worksheet.write(1, 2, 'GPS Destination')
	worksheet.write(1, 3, 'Sources')
	worksheet.write(1, 4, 'Destination')
	worksheet.write(1, 5, 'Intel url')
	worksheet.write(1, 6, 'GMap Sources')
	worksheet.write(1, 7, 'GMap Destination')
	initialRow = 2
	zoom=intelurl.split('=')[2].split('&')[0]
	for link in intelurl.split('=')[3].split('_'):
		# Ordre
		worksheet.write(initialRow, 0, 'Lien 0'+ str(initialRow - 1))
		# GPS Sources
		worksheet.write(initialRow, 1, link.split(',')[0]+','+link.split(',')[1])
		# GPS Destination
		worksheet.write(initialRow, 2, link.split(',')[2]+','+link.split(',')[3])
		# Emplacement reserve pour le nom des portails sources et destination
		worksheet.write(initialRow, 3, '')
		worksheet.write(initialRow, 4, '')
		# Intel url
		moyx=(float(link.split(',')[0])+float(link.split(',')[2])) /2
		moyy=(float(link.split(',')[1])+float(link.split(',')[3])) /2
		worksheet.write(initialRow, 5, 'https://www.ingress.com/intel?ll='+str(moyx)+','+str(moyy)+'&z='+zoom+'&pls='+link)
		# GMap Sources
		worksheet.write(initialRow, 6, 'https://www.google.fr/maps/search/'+link.split(',')[0]+','+link.split(',')[1])
		# GMap Destinations
		worksheet.write(initialRow, 7, 'https://www.google.fr/maps/search/'+link.split(',')[2]+','+link.split(',')[3])
		initialRow += 1
	workbook.close()  
else:
	print('Mauvaise url!')