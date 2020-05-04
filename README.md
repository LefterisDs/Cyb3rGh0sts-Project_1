## Open eClass 2.3

Το repository αυτό περιέχει μια __παλιά και μη ασφαλή__ έκδοση του eclass.
Προορίζεται για χρήση στα πλαίσια του μαθήματος
[Προστασία & Ασφάλεια Υπολογιστικών Συστημάτων (ΥΣ13)](https://crypto.di.uoa.gr/csec/), __μην τη
χρησιμοποιήσετε για κάνενα άλλο σκοπό__.


## 2020 Project 1

Εκφώνηση: https://crypto.di.uoa.gr/csec/assets/projects/project1.pdf


### Μέλη ομάδας

- 1115201600042, Ελευθέριος Δημητράς
- 1115201600119, Μιχαήλ Ξανθόπουλος

### Report

Άμυνες :

Άμυνες σε XSS :  
α)Σε αρκετά σημεία όπως στο modules/agenda/myagenda.php?month=6&year=2020<script>xss</script>
χρησιμοποιήσαμε filtering , δηλαδή αν πάρουμε κάτι διαφορετικό εκτός από αριθμό στο year του URL , επιστρέφουμε τον 
χρήστη στο αρχικό modules/agenda/myagenda.php , με τον παρακάτω τρόπο
if (preg_match("/[^0-9]/", $year) or preg_match("/[^0-9]/" , $month) ) {
  header('Location: ./myagenda.php');
  exit();
}
else {
  $year = preg_replace("/[^0-9]/" , '' , $year);
  $month = preg_replace("/[^0-9]/", '' , $month);
}

β)Σε κάποια άλλα σημεία όπως στην τηλεσυνεργασία δεν θέλαμε να διαγράψουμε τελείως το μήνυμα του χρήστη σε περίπτωση
που έκανε xss παρά θέλαμε να διατηρηθεί το μήνυμα χωρίς να εκτελεστεί . Αυτό το πετύχαμε με το sanitization , δηλαδή
$chatLine = filter_var($chatLine , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
πράγμα που έκανε sanitize τους ειδικούς χαρακτήρες , οπότε ακόμα και αν ο χρήστης έγραφε <script>alert(...)</script>
θα εκτυπωνώταν στο τσατ το <script>alert(...)</script> , αλλά δεν θα εκτελείτο .





- Να εξηγεί τι είδους επιθέσεις δοκιμάσατε στο αντίπαλο site και αν αυτές πέτυχαν.
