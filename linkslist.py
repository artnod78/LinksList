import xlsxwriter

print('saisir une url')
intelurl = input()
if intelurl.startswith('https://www.ingress.com/intel?ll=') and intelurl.find('_'):
	print()

	workbook = xlsxwriter.Workbook('hello.xlsx')
	worksheet = workbook.add_worksheet('Liste des links')
	titre = workbook.add_format({'bold': True, 'font_color': 'white', 'font_size': 16, 'bg_color': '#9bbb59'})
	worksheet.write(0, 0, 'Ordre de tir ( a respecter scrupuleusement )', titre)
	initialRow = 2
	worksheet.add_table('A2:H'+str(len(intelurl.split('=')[3].split('_'))+initialRow),{'columns': [{'header': 'Ordre'}, {'header': 'GPS Sources'}, {'header': 'GPS Destination'}, {'header': 'GPS Destination'}, {'header': 'Sources'}, {'header': 'Destination'}, {'header': 'Intel url'}, {'header': 'GMap Sources'}, {'header': 'GMap Destination'}]})#, 'style': 'Table Style Light 11'
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