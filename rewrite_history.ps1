$hashes = git log --reverse --format=%H main
git checkout --orphan clean-history
foreach ($h in $hashes) {
    git checkout $h -- .
    $msg = git log -1 --format=%B $h
    git add -A
    git commit -m $msg --author="Kiss Bercel <kiss.berci20@gmail.com>"
}
git branch -M main
