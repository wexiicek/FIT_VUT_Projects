#!/usr/bin/env python3

#checks, whether input can or cannot be a set
#returns item if yes
#returns frozenset if not
def can_be_a_set_member_or_frozenset(item):
    try:
        {item}
        return item
    except:
        return frozenset(item)

#takes a list as input
#returns all subsets as output
#including empty
def all_subsets(lst):
    result = [[]]
    for listItem in lst:
        result.extend([subset + [listItem] for subset in result])
    return result

#takes a list as input
#returns [all] subsets as output
#based on input:
#either doesnt return the empty list
#or it returns the empty list
def all_subsets_excl_empty(*argv, **kwarg):
    exclude_empty=kwarg.get('exclude_empty', True)
    if exclude_empty:
        sub=all_subsets(argv)
        del sub[0]
        return sub
    else:
        return all_subsets(argv)