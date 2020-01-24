# seqRenameAddDigit
Reanme files with one more leading digit
<pre>
usage: seqRenameAddDigit <dir> [start]
Renames files in dir by adding one more digit in their number part. Ex: IMG_1599.jpg => IMG_01599.jpg
If [start] is defined, add a leading 0 if file number part > start, or a 1 if not. If we have :
	a01.jpg  a02.jpg  a98.jpg  a99.jpg
with start=50 it will become:
	a098.jpg a099.jpg a101.jpg a102.jpg
</pre>
