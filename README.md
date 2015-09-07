# MR_XML_Reader
An extension of PHP XMLReader to read an XML file and convert into an associative array

I Know we can use SimpleXMLElement() to convert an XML to object. But the problem starts when the XML starts to get bigger.
AS SimpleXML loads the whole XML file into memory before starting to work with it.

The solution is XMLReader class. But the problem with XMLReader is you have to do everyting by yourself. This class is
just an extension of XMLReader to read XML one element at a time and try to get all the sub elements, attributes and values
and organize them in an associative array.

If the XML is big, this class will throw fatal out of memory exception same as SimpleXML. But this can be
avoided by not saving the data in the array. As the fatal error occurs because php cannot allocate any more memory
for the output array. So for really big XML files, just do not save it to an Array just simply return it and use it.
