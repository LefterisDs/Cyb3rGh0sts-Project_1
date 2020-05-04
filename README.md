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

Άμυνες από την πλευρά του server :
Αρχικά βάλαμε σε πολλους φακέλους(όπως πχ στο /modules/admin/mysql),που απαγορευεται κανονικά η πρόσβαση απο απλούς χρήστες, 
ένα .htaccess έτσι ώστε να τους προστατεύσουμε . Σε κάποια από αυτά βάλαμε "Deny from all" και στου /modules/admin/mysql
βάλαμε έναν κωδικό με κάποιον χρήστη που φτιάξαμε .

Άμυνες σε XSS :  
α)Σε αρκετά σημεία όπως στο modules/agenda/myagenda.php?month=6&year=2020<script>xss</script>
χρησιμοποιήσαμε cut filtering , δηλαδή αν πάρουμε κάτι διαφορετικό εκτός από αριθμό στο year του URL , επιστρέφουμε τον 
χρήστη στο αρχικό modules/agenda/myagenda.php , με τον παρακάτω τρόπο\
```
if (preg_match("/[^0-9]/", $year) or preg_match("/[^0-9]/" , $month) ) {
  header('Location: ./myagenda.php');
  exit();
}
else {
  $year = preg_replace("/[^0-9]/" , '' , $year);
  $month = preg_replace("/[^0-9]/", '' , $month);
}
```

β)Σε κάποια άλλα σημεία όπως στην τηλεσυνεργασία δεν θέλαμε να διαγράψουμε τελείως το μήνυμα του χρήστη σε περίπτωση
που έκανε xss παρά θέλαμε να διατηρηθεί το μήνυμα χωρίς να εκτελεστεί . Αυτό το πετύχαμε με το sanitization , δηλαδή\
```$chatLine = filter_var($chatLine , FILTER_SANITIZE_FULL_SPECIAL_CHARS);```
πράγμα που έκανε sanitize τους ειδικούς χαρακτήρες , οπότε ακόμα και αν ο χρήστης έγραφε <script>alert(...)</script>
θα εκτυπωνώταν στο τσατ το <script>alert(...)</script> , αλλά δεν θα εκτελείτο .

γ)Άλλες φορές χρησιμοποιήσαμε το απλό filtering δηλαδή\
```
$email = filter_var($email , FILTER_SANITIZE_EMAIL);
$uname = preg_replace("/[^A-Za-z0-9]/", '', $uname);
$nom_form = preg_replace("/[^A-Za-z0-9]/", '', $nom_form);
$prenom_form = preg_replace("/[^A-Za-z0-9]/", '', $prenom_form);
$am = preg_replace("/[^0-9]/", '', $am);
```
δ)Τέλος αν για κάποιο λόγο κάποιος έγραφε στο URL πχ http://cybergh0sts.chatzi.org/index.php/""> μπορούσε
να κάνει GET request στο site και να τυπώσει κάτι , οπότε μπορούσε να κάνει και xss . Προκειμένου να το διορθώσουμε
αυτό κόβαμε οποιουσδήποτε χαρακτήρες υπάρχουν μετά το .php με τον παρακάτω κώδικα , ο οποίος προστέθηκε πάνω πάνω 
σε κάθε αρχείο \
if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){\
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");\
	exit();\
}

Άμυνες σε SQLi :\
α)Σε αρκετά σημεία όπως στο modules/auth/contactadmin.php?userid=1
χρησιμοποιήσαμε cut filtering , δηλαδή αν πάρουμε κάτι διαφορετικό εκτός από αριθμό στο userid του URL , επιστρέφουμε τον 
χρήστη στο αρχικό index.php , με τον παρακάτω τρόπο
```
if (preg_match("/[^0-9]/", $userid)){
	header("Location: ../index.php");
	exit();
}
```

β)Σε άλλα σημεία χρησιμοποιήσαμε το full filtering σε συνδυασμό με το preg_match δηλαδή :\
```
if (preg_match("/[^0-9]/", $id)){
	unset($id);
	show_student_assignments();
}
else {
	$id = preg_replace("/[^0-9]/", '', $id);
```
ή πχ στο upgrade/upgrade_functions.php\
```
if (preg_match("/[^a-zA-Z]/", $username) ){
	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

$username = preg_replace("/[^a-zA-Z]/", '',  $username);
```

Άμυνες σε CSRF :\
α)Για την άμυνα ενάντια σε csrf πάνω σε φόρμες χρησιμοποιήσαμε tokens τα οποία τα βάζαμε ως hidden element της φόρμας δηλαδή\
<input type=\"hidden\" name=\"token\" value=".$_SESSION['tok'].">\
Για την δημιουργία του token φτιάξαμε την παρακάτω συνάρτηση που ανάλογα με το n παράγει μια τυχαία συμβολοσειρά με μήκος n\
```
function get_rand_pwd($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
  
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
  
    return $randomString;
} 
```
To token αρχικοποιείται στο index όπως φαίνεται παρακάτω :\
```
$n=25; 
$fakepwd = get_rand_pwd($n);
$_SESSION['tok'] = $fakepwd;
```
- Να εξηγεί τι είδους επιθέσεις δοκιμάσατε στο αντίπαλο site και αν αυτές πέτυχαν.
